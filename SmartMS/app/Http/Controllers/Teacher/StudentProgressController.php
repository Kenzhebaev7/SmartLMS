<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SectionController as StudentSectionController;
use App\Models\Achievement;
use App\Models\Lesson;
use App\Models\Result;
use App\Models\Section;
use App\Models\SectionMaster;
use App\Models\TeacherFeedback;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentProgressController extends Controller
{
    public function index(Request $request): View
    {
        $students = User::where('role', User::ROLE_STUDENT)
            ->withCount('results')
            ->orderBy('name')
            ->get();

        $sections = Section::with(['quiz', 'lessons'])
            ->orderBy('grade')
            ->orderByDesc('is_featured')
            ->orderBy('order')
            ->get();

        $resultsByUser = Result::where('passed', true)->get()->groupBy('user_id');
        $masters = SectionMaster::with(['user', 'section'])->get();
        $feedbacks = TeacherFeedback::where('teacher_id', $request->user()->id)
            ->get()
            ->keyBy('student_id');
        $achievements = Achievement::orderBy('name')->get();

        $lessonProgressCards = [];
        foreach ($students as $student) {
            $studentSections = StudentSectionController::sectionsForUser($student)->load('quiz', 'lessons.unlockAfterLesson');
            $completedLessonIds = StudentSectionController::completedLessonIdsForStudent($student);
            $studentSectionsData = [];
            $completedLessonsCount = 0;
            $totalLessonsCount = 0;

            foreach ($studentSections as $section) {
                $lessons = Lesson::dedupeForDisplay($section->lessons)->values();
                $lessonItems = [];

                foreach ($lessons as $lesson) {
                    $completed = StudentSectionController::isLessonCompletedByStudent($student, $lesson, $completedLessonIds);
                    $unlocked = StudentSectionController::isLessonUnlockedForStudent($student, $lesson, $completedLessonIds);

                    $lessonItems[] = [
                        'lesson' => $lesson,
                        'completed' => $completed,
                        'unlocked' => $unlocked,
                        'unlock_after_lesson' => $lesson->unlockAfterLesson,
                    ];

                    $totalLessonsCount++;
                    if ($completed) {
                        $completedLessonsCount++;
                    }
                }

                if ($lessonItems !== []) {
                    $studentSectionsData[] = [
                        'section' => $section,
                        'lessons' => $lessonItems,
                    ];
                }
            }

            $lessonProgressCards[$student->id] = [
                'completedLessonsCount' => $completedLessonsCount,
                'totalLessonsCount' => $totalLessonsCount,
                'sections' => $studentSectionsData,
            ];
        }

        return view('teacher.progress.index', [
            'students' => $students,
            'sections' => $sections,
            'resultsByUser' => $resultsByUser,
            'masters' => $masters,
            'feedbacks' => $feedbacks,
            'achievements' => $achievements,
            'lessonProgressCards' => $lessonProgressCards,
        ]);
    }

    public function storeFeedback(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $student = User::findOrFail($data['student_id']);
        if ($student->role !== User::ROLE_STUDENT) {
            abort(403);
        }

        TeacherFeedback::updateOrCreate(
            [
                'teacher_id' => $request->user()->id,
                'student_id' => $data['student_id'],
            ],
            ['body' => $data['body'] ?? '']
        );

        return back()->with('status', __('messages.feedback_saved'));
    }

    public function assignMaster(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'section_id' => ['required', 'exists:sections,id'],
        ]);

        SectionMaster::firstOrCreate(
            ['user_id' => $data['user_id'], 'section_id' => $data['section_id']],
            ['assigned_by' => $request->user()->id]
        );

        return back()->with('status', __('messages.master_assigned'));
    }

    public function awardAchievement(Request $request, User $student): RedirectResponse
    {
        $data = $request->validate([
            'achievement_key' => ['required', 'string', 'max:64'],
        ]);

        if ($student->role !== User::ROLE_STUDENT) {
            abort(403);
        }

        $service = app(AchievementService::class);
        $awarded = $service->award($student, $data['achievement_key']);

        return back()->with(
            'status',
            $awarded ? __('messages.achievement_awarded') : __('messages.achievement_already_or_invalid')
        );
    }
}
