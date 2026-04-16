<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Result;
use App\Models\Section;
use App\Models\TeacherFeedback;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_open_core_pages(): void
    {
        $teacher = $this->makeTeacher();
        $section = Section::create([
            'title' => 'Algorithms',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Intro lesson',
            'content' => 'Content',
            'order' => 1,
        ]);
        $quiz = Quiz::create([
            'section_id' => $section->id,
            'title' => 'Quiz title',
            'passing_percent' => 70,
        ]);
        Question::create([
            'quiz_id' => $quiz->id,
            'text' => 'Question text',
            'type' => 'single',
            'options' => ['A' => 'Yes', 'B' => 'No'],
            'correct_answer' => ['A'],
            'order' => 0,
        ]);

        $this->actingAs($teacher)->get(route('teacher.dashboard'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.sections.index'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.sections.show', $section))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.sections.lessons.create', $section))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.lessons.edit', $lesson))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.sections.quiz.edit', $section))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.progress.index'))->assertOk();
        $this->actingAs($teacher)->get(route('teacher.certificates.index'))->assertOk();
    }

    public function test_teacher_can_manage_sections_lessons_and_quizzes(): void
    {
        $teacher = $this->makeTeacher();

        $this->actingAs($teacher)
            ->post(route('teacher.sections.store'), [
                'title' => 'New section',
                'title_kk' => 'Zhаna bolim',
                'description' => 'Section description',
                'order' => 1,
                'grade' => 10,
                'is_revision' => '1',
            ])
            ->assertRedirect(route('teacher.sections.index'))
            ->assertSessionHasNoErrors();

        $section = Section::where('title', 'New section')->firstOrFail();
        $this->assertTrue($section->is_revision);

        $this->actingAs($teacher)
            ->post(route('teacher.sections.lessons.store', $section), [
                'title' => 'Lesson 1',
                'content' => 'Lesson body',
                'order' => 1,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            ])
            ->assertRedirect(route('teacher.sections.show', $section))
            ->assertSessionHasNoErrors();

        $lesson = Lesson::where('section_id', $section->id)->firstOrFail();
        $this->assertSame('dQw4w9WgXcQ', $lesson->video_id);

        $this->actingAs($teacher)
            ->post(route('teacher.sections.lessons.store', $section), [
                'title' => 'Lesson 2',
                'content' => 'Second lesson body',
                'order' => 2,
                'unlock_after_lesson_id' => $lesson->id,
            ])
            ->assertRedirect(route('teacher.sections.show', $section))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lessons', [
            'section_id' => $section->id,
            'title' => 'Lesson 2',
            'unlock_after_lesson_id' => $lesson->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.sections.quiz.update', $section), [
                'title' => 'Section quiz',
                'passing_percent' => 75,
                'questions' => [
                    [
                        'text' => 'Is PHP a server language?',
                        'type' => 'single',
                        'options' => '{"A":"Yes","B":"No"}',
                        'correct_answer' => 'A',
                    ],
                ],
            ])
            ->assertRedirect(route('teacher.sections.show', $section))
            ->assertSessionHasNoErrors();

        $quiz = $section->fresh()->quiz;
        $this->assertNotNull($quiz);
        $this->assertSame('Section quiz', $quiz->title);
        $this->assertSame(75, $quiz->passing_percent);
        $this->assertCount(1, $quiz->questions);
    }

    public function test_teacher_can_manage_student_progress_and_certificates(): void
    {
        Storage::fake('public');

        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();
        $section = Section::create([
            'title' => 'Project section',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $achievement = Achievement::create([
            'key' => 'first-master',
            'name' => 'First Master',
            'description' => 'Awarded by teacher',
            'xp' => 10,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.progress.feedback'), [
                'student_id' => $student->id,
                'body' => 'Good progress',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('teacher_feedback', [
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'body' => 'Good progress',
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.progress.master'), [
                'user_id' => $student->id,
                'section_id' => $section->id,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('section_masters', [
            'user_id' => $student->id,
            'section_id' => $section->id,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.students.achievements.award', $student), [
                'achievement_key' => $achievement->key,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $student->id,
            'achievement_id' => $achievement->id,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.certificates.store'), [
                'user_id' => $student->id,
                'title' => 'Course Certificate',
                'description' => 'Completed course',
                'awarded_at' => '2026-04-14',
                'file' => UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf'),
            ])
            ->assertRedirect(route('teacher.certificates.index'))
            ->assertSessionHasNoErrors();

        $certificate = Certificate::firstOrFail();
        $this->assertSame($teacher->id, $certificate->teacher_id);
        Storage::disk('public')->assertExists($certificate->file_path);

        $this->actingAs($teacher)
            ->delete(route('teacher.certificates.destroy', $certificate))
            ->assertRedirect(route('teacher.certificates.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('certificates', ['id' => $certificate->id]);
    }

    public function test_student_dashboard_counts_completed_sections_consistently(): void
    {
        $student = $this->makeStudent();

        $section1 = Section::create([
            'title' => 'Section 1',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $section2 = Section::create([
            'title' => 'Section 2',
            'order' => 2,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $section3 = Section::create([
            'title' => 'Section 3',
            'order' => 3,
            'grade' => 9,
            'is_revision' => false,
        ]);

        Quiz::create([
            'section_id' => $section1->id,
            'title' => 'Quiz 1',
            'passing_percent' => 70,
        ]);
        Quiz::create([
            'section_id' => $section2->id,
            'title' => 'Quiz 2',
            'passing_percent' => 70,
        ]);

        $lesson = Lesson::create([
            'section_id' => $section3->id,
            'title' => 'Final lesson',
            'content' => 'Lesson content',
            'order' => 1,
        ]);

        foreach ([$section1, $section2] as $section) {
            Result::create([
                'user_id' => $student->id,
                'quiz_id' => $section->quiz->id,
                'score' => 100,
                'passed' => true,
                'attempted_at' => now(),
            ]);
        }

        LessonProgress::create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'lesson_key' => $lesson->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($student)->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewHas('sectionsPassedCount', 3);
    }

    public function test_teacher_progress_shows_completed_section_without_quiz(): void
    {
        $teacher = $this->makeTeacher();
        $student = $this->makeStudent();

        $section = Section::create([
            'title' => 'No Quiz Section',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Only lesson',
            'content' => 'Body',
            'order' => 1,
        ]);

        LessonProgress::create([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
            'lesson_key' => $lesson->id,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.progress.index'));

        $response->assertOk();
        $response->assertSee($student->name);
        $response->assertSee('Only lesson');
        $response->assertSee(__('messages.teacher_lessons_progress_label', ['completed' => 1, 'total' => 1]));
    }

    public function test_student_cannot_open_locked_lesson_until_prerequisite_is_completed(): void
    {
        $student = $this->makeStudent();

        $section = Section::create([
            'title' => 'Sequential section',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);
        $firstLesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'First lesson',
            'content' => 'Body',
            'order' => 1,
        ]);
        $secondLesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Second lesson',
            'content' => 'Body',
            'order' => 2,
            'unlock_after_lesson_id' => $firstLesson->id,
        ]);

        $this->actingAs($student)
            ->get(route('lessons.show', [$section, $secondLesson]))
            ->assertForbidden();

        LessonProgress::create([
            'user_id' => $student->id,
            'lesson_id' => $firstLesson->id,
            'lesson_key' => $firstLesson->id,
            'completed_at' => now(),
        ]);

        $this->actingAs($student)
            ->get(route('lessons.show', [$section, $secondLesson]))
            ->assertOk();
    }

    public function test_admin_can_manage_lessons_and_quizzes_from_content_panel(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $section = Section::create([
            'title' => 'Admin content',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('teacher.sections.lessons.create', $section))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('teacher.sections.lessons.store', $section), [
                'title' => 'Admin lesson',
                'content' => 'Body',
                'order' => 1,
            ])
            ->assertRedirect(route('teacher.sections.show', $section))
            ->assertSessionHasNoErrors();

        $this->actingAs($admin)
            ->put(route('teacher.sections.quiz.update', $section), [
                'title' => 'Admin quiz',
                'passing_percent' => 70,
                'questions' => [
                    [
                        'text' => 'Question',
                        'type' => 'single',
                        'options' => '{"A":"Yes","B":"No"}',
                        'correct_answer' => 'A',
                    ],
                ],
            ])
            ->assertRedirect(route('teacher.sections.show', $section))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('lessons', ['section_id' => $section->id, 'title' => 'Admin lesson']);
        $this->assertDatabaseHas('quizzes', ['section_id' => $section->id, 'title' => 'Admin quiz']);
    }

    public function test_forum_index_hides_duplicate_topics_with_same_title(): void
    {
        $student = $this->makeStudent();
        $section = Section::create([
            'title' => 'C++ basics',
            'order' => 1,
            'grade' => 9,
            'is_revision' => false,
        ]);

        Thread::create([
            'user_id' => $student->id,
            'section_id' => $section->id,
            'title' => 'Основы C++',
            'body' => 'First body',
        ]);
        Thread::create([
            'user_id' => $student->id,
            'section_id' => $section->id,
            'title' => '  основы c++  ',
            'body' => 'Duplicate body',
        ]);

        $response = $this->actingAs($student)->get(route('forum.index'));

        $response->assertOk();
        $this->assertCount(1, $response->viewData('threads'));
    }

    private function makeTeacher(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);
    }

    private function makeStudent(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_STUDENT,
            'grade' => 9,
            'placement_passed' => true,
        ]);
    }
}
