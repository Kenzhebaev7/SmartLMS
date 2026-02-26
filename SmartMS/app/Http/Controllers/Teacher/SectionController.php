<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index(): View
    {
        $sections = Section::orderBy('order')->get();
        return view('teacher.sections.index', ['sections' => $sections]);
    }

    public function create(): View
    {
        return view('teacher.sections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_kk' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'level' => ['nullable', 'in:beginner,advanced'],
        ]);
        $data['order'] = $data['order'] ?? Section::max('order') + 1;
        $data['level'] = $data['level'] ?? null;
        Section::create($data);
        return redirect()->route('teacher.sections.index')->with('status', __('messages.section_created'));
    }

    public function show(Section $section): View
    {
        $section->load('lessons', 'quiz.questions');
        return view('teacher.sections.show', ['section' => $section]);
    }

    public function edit(Section $section): View
    {
        return view('teacher.sections.edit', ['section' => $section]);
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_kk' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'description_kk' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'level' => ['nullable', 'in:beginner,advanced'],
        ]);
        $data['level'] = $data['level'] ?? null;
        $section->update($data);
        return redirect()->route('teacher.sections.index')->with('status', 'Раздел обновлён.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();
        return redirect()->route('teacher.sections.index')->with('status', __('messages.section_deleted'));
    }
}
