<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->get('q', ''));
        $results = collect();

        if ($query !== '') {
            $like = '%' . $query . '%';
            $results = Lesson::query()
                ->with('section')
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('title_kk', 'like', $like)
                        ->orWhere('content', 'like', $like)
                        ->orWhere('content_kk', 'like', $like);
                })
                ->orderBy('section_id')
                ->orderBy('order')
                ->limit(50)
                ->get();
        }

        return view('search.index', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}

