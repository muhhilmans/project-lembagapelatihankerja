<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        // Query users that have either 'majikan' or 'pembantu' role
        $query = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['majikan', 'pembantu']);
        })->with(['roles', 'receivedReviews.reviewer.roles']);

        // Apply filters
        $filter = $request->input('filter', 'semua');

        if ($filter === 'majikan') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'majikan');
            })->has('receivedReviews');
        } elseif ($filter === 'pembantu') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'pembantu');
            })->has('receivedReviews');
        } elseif ($filter === 'majikan_null') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'majikan');
            })->doesntHave('receivedReviews');
        } elseif ($filter === 'pembantu_null') {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'pembantu');
            })->doesntHave('receivedReviews');
        }

        $users = $query->latest()->get();

        return view('cms.reviews.index', compact('users', 'filter'));
    }
}
