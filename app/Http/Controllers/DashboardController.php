<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Complaint;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $applications = Application::with('vacancy')->get();
        $complaints = Complaint::all();

        $pendingApp = $applications->where('status', 'pending')->count();
        $processApp = $applications->whereIn('status', ['interview', 'schedule', 'verify', 'contract'])->count();
        $acceptedApp = $applications->where('status', 'accepted')->count();
        $rejectedApp = $applications->whereIn('status', ['rejected', 'laidoff'])->count();
        $vacancy = Vacancy::where('status', true)->count();
        $worker = Application::whereIn('status', ['accepted', 'review'])->count();
        $rejectedComp = $complaints->where('status', 'rejected')->count();
        $acceptedComp = $complaints->where('status', 'accepted')->count();
        $datasApp = $applications->where('status', 'interview')->sortByDesc('updated_at');

        $data = [
            'pending' => $pendingApp,
            'process' => $processApp,
            'accepted' => $acceptedApp,
            'rejected' => $rejectedApp,
            'vacancy' => $vacancy,
            'worker' => $worker,
            'rejectedComp' => $rejectedComp,
            'acceptedComp' => $acceptedComp,
        ];

        return view('cms.dashboard.dashboard', compact(['data', 'datasApp']));
    }

    public function dashboardEmploye()
    {
        $applications = Application::with('vacancy')
            ->where('employe_id', auth()->user()->id)
            ->orWhereHas('vacancy', function ($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->get();
        $complaints = Complaint::where('employe_id', auth()->user()->id)->get();

        $pendingApp = $applications->where('status', 'pending')->count();
        $processApp = $applications->whereIn('status', ['interview', 'schedule', 'verify', 'contract'])->count();
        $acceptedApp = $applications->where('status', 'accepted')->count();
        $rejectedApp = $applications->whereIn('status', ['rejected', 'laidoff'])->count();
        $vacancy = Vacancy::where('user_id', auth()->user()->id)->where('status', true)->count();
        $worker = Application::whereIn('status', ['accepted', 'review'])
            ->where(function ($query) {
                $query->where('employe_id', auth()->user()->id)
                    ->orWhereHas('vacancy.user', function ($q) {
                        $q->where('id', auth()->user()->id);
                    });
            })->count();
        $rejectedComp = $complaints->where('status', 'rejected')->count();
        $acceptedComp = $complaints->where('status', 'accepted')->count();
        $datasApp = $applications->where('status', 'interview')->sortByDesc('updated_at');

        $data = [
            'pending' => $pendingApp,
            'process' => $processApp,
            'accepted' => $acceptedApp,
            'rejected' => $rejectedApp,
            'vacancy' => $vacancy,
            'worker' => $worker,
            'rejectedComp' => $rejectedComp,
            'acceptedComp' => $acceptedComp,
        ];

        return view('cms.dashboard.dashboard-employe', compact(['data', 'datasApp']));
    }

    public function dashboardServant()
    {
        $applications = Application::where('servant_id', auth()->user()->id)->get();
        $pending = $applications->where('status', 'pending')->count();
        $process = $applications->whereIn('status', ['interview', 'schedule', 'verify', 'contract'])->count();
        $accepted = $applications->where('status', 'accepted')->count();
        $rejected = $applications->whereIn('status', ['rejected', 'laidoff'])->count();

        $data = [
            'pending' => $pending,
            'process' => $process,
            'accepted' => $accepted,
            'rejected' => $rejected
        ];

        return view('cms.dashboard.dashboard-servant', compact('data'));
    }
}
