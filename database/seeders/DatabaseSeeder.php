<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Institution
        $institutionId = DB::table('institutions')->insertGetId([
            'name' => 'National Institute of Tech',
            'domain' => 'nit.edu',
            'logo' => 'nit_logo.png',
            'is_active' => true,
            'settings' => json_encode(['theme' => 'dark', 'allow_registration' => true]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Users (Teacher & Student)
        $teacherId = DB::table('users')->insertGetId([
            'institution_id'   => $institutionId,
            'full_name'        => 'Yun Dalin',
            'email'            => 'yundalin9999@gmail.com',
            'password_hash'    => Hash::make('password123'), // ✅ MATCHES MIGRATION
            'role'             => 'teacher',
            'status'           => 'active',
            'institutional_id' => 'FAC-8842-1092',           // ✅ MATCHES MIGRATION
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        $studentId = DB::table('users')->insertGetId([
            'institution_id'   => $institutionId,
            'full_name'        => 'John Doe',
            'email'            => 'john@nit.edu',
            'password_hash'    => Hash::make('password123'), // ✅ MATCHES MIGRATION
            'role'             => 'student',
            'status'           => 'active',
            'institutional_id' => 'STU-1122-3344',           // ✅ MATCHES MIGRATION
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // 3. Seed Course
        $courseId = DB::table('courses')->insertGetId([
            'name' => 'Introduction to Data Science',
            'code' => 'DS101',
            'description' => 'Learn foundational data analysis concepts and tools.',
            'institution_id' => $institutionId,
            'teacher_id' => $teacherId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Seed Enrollment
        DB::table('enrollments')->insert([
            'user_id' => $studentId,
            'course_id' => $courseId,
            'enrolled_at' => now(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Seed Exam (Uses UUID)
        $examId = (string) Str::uuid();
        DB::table('exams')->insert([
            'exam_id' => $examId,
            'title' => 'Midterm Exam - Data Science',
            'course_id' => $courseId,
            'created_by' => $teacherId,
            'duration' => 60,
            'pass_mark' => 50.00,
            'instructions' => 'Answer all questions. Do not refresh your browser session.',
            'status' => 'published',
            'start_time' => now()->subMinutes(30),
            'end_time' => now()->addHours(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Seed Question Bank
        $bankId = DB::table('question_banks')->insertGetId([
            'name' => 'Core Programming Pool',
            'description' => 'A bank containing introductory database and logic questions.',
            'institution_id' => $institutionId,
            'created_by' => $teacherId,
            'subject' => 'Computer Science',
            'difficulty' => 'medium',
            'tags' => json_encode(['sql', 'basics']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 7. Seed Question
        $questionId = DB::table('questions')->insertGetId([
            'exam_id' => $examId,
            'question_bank_id' => $bankId,
            'type' => 'multiple_choice',
            'content' => 'What language is used primarily for querying relational databases?',
            'options' => json_encode(['A' => 'PHP', 'B' => 'SQL', 'C' => 'Python', 'D' => 'HTML']),
            'correct_answer' => json_encode(['B']),
            'marks' => 10.00,
            'order' => 1,
            'explanation' => 'SQL stands for Structured Query Language used in RDBMS systems.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Seed Exam Session
        $sessionId = DB::table('exam_sessions')->insertGetId([
            'exam_id' => $examId,
            'user_id' => $studentId,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Chrome/120.0.0',
            'status' => 'completed',
            'joined_at' => now()->subMinutes(25),
            'left_at' => now()->subMinutes(5),
            'flags' => json_encode(['tab_switches' => 0]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 9. Seed Submission
        $submissionId = DB::table('submissions')->insertGetId([
            'exam_id' => $examId,
            'user_id' => $studentId,
            'session_id' => $sessionId,
            'started_at' => now()->subMinutes(25),
            'submitted_at' => now()->subMinutes(5),
            'time_taken_seconds' => 1200,
            'status' => 'graded',
            'total_score' => 10.00,
            'percentage' => 100.00,
            'is_passed' => true,
            'teacher_feedback' => 'Excellent work!',
            'graded_by' => $teacherId,
            'graded_at' => now()->subMinutes(2),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 10. Seed Answer
        DB::table('answers')->insert([
            'submission_id' => $submissionId,
            'question_id' => $questionId,
            'answer_content' => json_encode(['B']),
            'is_correct' => true,
            'marks_awarded' => 10.00,
            'teacher_comment' => 'Correct choice.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 11. Seed Notification
        DB::table('notifications')->insert([
            'user_id' => $studentId,
            'title' => 'Exam Graded',
            'body' => 'Your submission for Midterm Exam has been graded.',
            'type' => 'grade_alert',
            'data' => json_encode(['submission_id' => $submissionId]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 12. Seed Report
        DB::table('reports')->insert([
            'institution_id' => $institutionId,
            'generated_by' => $teacherId,
            'type' => 'course_performance',
            'payload' => json_encode(['average_score' => 85, 'passed_count' => 1]),
            'period_start' => now()->subDays(30),
            'period_end' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 13. Seed Audit Log
        DB::table('audit_logs')->insert([
            'user_id' => $teacherId,
            'institution_id' => $institutionId,
            'action' => 'grade_exam',
            'model_type' => 'App\Models\Submission',
            'model_id' => $submissionId,
            'payload' => json_encode(['score' => 10]),
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);
    }
}