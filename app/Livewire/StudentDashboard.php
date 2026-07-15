<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\Enrollment;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;

class StudentDashboard extends Component
{
    public $activeTab = 'upcoming';
    public $search = '';
    
    public $showModal = false;
    public $selectedExamDetails = null;

    protected $listeners = [
        'echo:exams,ExamUpdated' => '$refresh',
        'echo:exams,ExamCreated' => '$refresh'
    ];

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function viewExamDetails($examId)
    {
        $this->selectedExamDetails = Exam::with('course')->find($examId);
        if ($this->selectedExamDetails) {
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedExamDetails = null;
    }

    public function render()
    {
        $user = Auth::user();

        // Pull active course IDs the student is enrolled in[cite: 2]
        $enrolledCourseIds = Enrollment::where('user_id', $user->user_id)
            ->where('status', 'active')
            ->pluck('course_id');

        // Real-Time Overview Metrics calculations[cite: 2]
        $totalExamsCount = Exam::whereIn('course_id', $enrolledCourseIds)->where('status', 'published')->count();
        $completedExamsCount = Submission::where('user_id', $user->user_id)->count();
        $averageScorePercent = Submission::where('user_id', $user->user_id)->avg('percentage') ?? 0;

        $examQuery = Exam::with('course')
            ->whereIn('course_id', $enrolledCourseIds)
            ->where('status', 'published')
            ->when(!empty($this->search), function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            });

        if ($this->activeTab === 'upcoming') {
            $exams = (clone $examQuery)->where('start_time', '>', now())->orderBy('start_time', 'asc')->get();
        } elseif ($this->activeTab === 'ongoing') {
            $exams = (clone $examQuery)->where('start_time', '<=', now())->where('end_time', '>=', now())->get();
        } else {
            $exams = Submission::with('exam.course')
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.student-dashboard', [
            'exams' => $exams,
            'user' => $user,
            'totalExamsCount' => $totalExamsCount,
            'completedExamsCount' => $completedExamsCount,
            'averageScorePercent' => $averageScorePercent
        ]);
    }
}