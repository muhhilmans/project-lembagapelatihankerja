<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Pengaduan; // Updated
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        if (auth()->user()->roles->first()->name == 'owner') {
            $filter = $request->get('filter', 'monthly');
        } else {
            $filter = $request->get('filter', 'weekly');
        }

        switch ($filter) {
            case 'monthly':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                break;

            case 'yearly':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;

            default:
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
        }

        $applications = Application::with('vacancy')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $complaints = Pengaduan::whereBetween('created_at', [$startDate, $endDate])->get();
        $vacancies = Vacancy::whereBetween('created_at', [$startDate, $endDate])->get();

        $pendingApp = $applications->where('status', 'pending')->count();
        $processApp = $applications->whereIn('status', ['interview', 'schedule', 'verify', 'contract'])->count();
        $acceptedApp = $applications->where('status', 'accepted')->count();
        $rejectedApp = $applications->whereIn('status', ['rejected', 'laidoff'])->count();
        $vacancy = $vacancies->where('status', true)->count();
        $worker = $applications->whereIn('status', ['accepted', 'review'])->count();
        
        // Map new statuses to old metrics for view compatibility
        $rejectedComp = 0; // 'rejected' status no longer exists
        $acceptedComp = $complaints->where('status', 'resolved')->count(); // Map resolved to accepted
        
        $datasApp = $applications->where('status', 'interview')->sortByDesc('updated_at');

        $chartWorker = $applications->whereIn('status', ['accepted', 'review'])->map(function ($item) {
            $sum = $item->servant->servantDetails->profession->name ?? 'Unknown';
            return [
                'worker_sum' => $sum,
            ];
        });

        $chartServant = $applications->whereIn('status', ['pending', 'interview', 'schedule', 'verify', 'contract'])->map(function ($item) {
            $sum = $item->servant->servantDetails->profession->name ?? 'Unknown';
            return [
                'servant_sum' => $sum,
            ];
        });

        $chartVacancy = $vacancies->where('status', true)->map(function ($item) {
            $sum = $item->profession->name ?? 'Unknown';
            return [
                'vacancy_sum' => $sum,
            ];
        });

        $chartWorkerCount = $chartWorker->groupBy('worker_sum')->map->count();
        $chartServantCount = $chartServant->groupBy('servant_sum')->map->count();
        $chartVacancyCount = $chartVacancy->groupBy('vacancy_sum')->map->count();

        $admins = User::where('is_active', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'admin');
            })->count();
        $employes = User::where('is_active', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'majikan');
            })->count();
        $servants = User::where('is_active', true)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'pembantu');
            })->count();

        $data = [
            'pending' => $pendingApp,
            'process' => $processApp,
            'accepted' => $acceptedApp,
            'rejected' => $rejectedApp,
            'vacancy' => $vacancy,
            'worker' => $worker,
            'rejectedComp' => $rejectedComp,
            'acceptedComp' => $acceptedComp,
            'admins' => $admins,
            'employes' => $employes,
            'servants' => $servants,
        ];

        $filterBar = $request->get('filterBar', 'weekly');
        $labelsBar = [];
        $activeWorkers = [];

        switch ($filterBar) {
            case 'monthly':
                $labelsBar = collect(range(1, 12))->map(function ($month) {
                    return now()->startOfYear()->addMonths($month - 1)->format('F');
                });
                $activeWorkers = $applications->where('status', 'accepted')->groupBy(function ($app) {
                    return $app->created_at->format('F');
                })->map->count();
                break;

            case 'yearly':
                $firstYear = Application::oldest('created_at')->value('created_at')->year ?? now()->year;
                $lastYear = Application::latest('created_at')->value('created_at')->year ?? now()->year;
                $labelsBar = range($firstYear, $lastYear);
                $activeWorkers = $applications->where('status', 'accepted')->groupBy(function ($app) {
                    return $app->created_at->year;
                })->map->count();
                break;

            default:
                $labelsBar = collect(range(1, 5))->map(function ($week) {
                    return "Minggu ke-$week";
                });
                $activeWorkers = $applications->where('status', 'accepted')->groupBy(function ($app) {
                    return $app->created_at->weekOfMonth;
                })->map->count();
                break;
        }

        return view('cms.dashboard.dashboard', compact('data', 'datasApp', 'chartWorkerCount', 'chartServantCount', 'chartVacancyCount', 'activeWorkers', 'filter', 'filterBar', 'labelsBar'));
    }

    public function dashboardEmploye()
    {
        $id = auth()->user()->id;
        $applications = Application::with('vacancy')
            ->where('employe_id', $id)
            ->orWhereHas('vacancy', function ($query) use ($id) {
                $query->where('user_id', $id);
            })
            ->get();
            
        // Get complaints involving this user
        $complaints = Pengaduan::where('reporter_id', $id)
                        ->orWhere('reported_user_id', $id)
                        ->get();

        $pendingApp = $applications->where('status', 'pending')->count();
        $processApp = $applications->whereIn('status', ['interview', 'schedule', 'verify', 'contract'])->count();
        $acceptedApp = $applications->where('status', 'accepted')->count();
        $rejectedApp = $applications->whereIn('status', ['rejected', 'laidoff'])->count();
        $vacancy = Vacancy::where('user_id', $id)->where('status', true)->count();
        $worker = Application::whereIn('status', ['accepted', 'review'])
            ->where(function ($query) use ($id) {
                $query->where('employe_id', $id)
                    ->orWhereHas('vacancy.user', function ($q) use ($id) {
                        $q->where('id', $id);
                    });
            })->count();
            
        $rejectedComp = 0; 
        $acceptedComp = $complaints->where('status', 'resolved')->count();
        
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

        $datasApp = $applications->where('status', 'interview')->sortByDesc('updated_at');

        $data = [
            'pending' => $pending,
            'process' => $process,
            'accepted' => $accepted,
            'rejected' => $rejected
        ];

        return view('cms.dashboard.dashboard-servant', compact(['data', 'datasApp']));
    }
}
