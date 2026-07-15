namespace App\Http\Controllers;

use App\Models\ExamSubmission;
use Illuminate\Http\Request;

class ExamGradingController extends Controller
{
    /**
     * Store or update a student's grade and redirect through the confirmation workflow.
     */
    public function store(Request $request, $id)
    {
        $submission = ExamSubmission::findOrFail($id);

        // 1. Validate incoming point allocations and overrides
        $validated = $request->validate([
            'q1_score' => 'required|integer|between:0,10',
            'q2_score' => 'required|integer|between:0,5',
            'accuracy' => 'required|integer|between:0,10',
            'depth'    => 'required|integer|between:0,10',
            'clarity'  => 'required|integer|between:0,5',
            'feedback' => 'nullable|string|max:1000',
            'action'   => 'required|string'
        ]);

        // 2. Calculate the Grand Total programmatically on the backend
        $totalScore = $validated['q1_score'] + 
                      $validated['q2_score'] + 
                      $validated['accuracy'] + 
                      $validated['depth'] + 
                      $validated['clarity'];

        // 3. Persist the state change into the centralized score table
        $submission->update([
            'q1_score'       => $validated['q1_score'],
            'q2_score'       => $validated['q2_score'],
            'accuracy_score' => $validated['accuracy'],
            'depth_score'    => $validated['depth'],
            'clarity_score'  => $validated['clarity'],
            'total_score'    => $totalScore,
            'feedback'       => $validated['feedback'],
            'status'         => 'graded',
        ]);

        // 4. Check action parameters to route to the correct blade view component
        if ($validated['action'] === 'save_next') {
            // Fetch next awaiting candidate pointer to advance the grading loop
            $nextStudent = ExamSubmission::where('status', 'pending')
                ->where('id', '>', $submission->id)
                ->first();

            if ($nextStudent) {
                return redirect()->route('teacher.grading.show', $nextStudent->id)
                    ->with('success', "Grade saved for {$submission->student_name}. Moving up next.");
            }
        }

        // Default: Fallback to grading_confirmation workflow tracking panel
        return redirect()->route('teacher.grading.confirmation', $submission->id)
            ->with('success', "Evaluation for {$submission->student_name} saved successfully.");
    }

    /**
     * Centralized Gradebook Screen: Easy to view all results, edit details, and post to students.
     */
    public function gradebook()
    {
        $submissions = ExamSubmission::orderBy('updated_at', 'desc')->get();
        
        // Statistical aggregations for the sidebar dashboard panel counters
        $totalStudents = $submissions->count();
        $completedCount = $submissions->where('status', 'graded')->count();
        $publishedCount = $submissions->where('is_published', true)->count();

        return view('teacher.gradebook', compact('submissions', 'totalStudents', 'completedCount', 'publishedCount'));
    }

    /**
     * Publish Single Score or Bulk Post Results instantly to Student Portals.
     */
    public function publish(Request $request, $id)
    {
        $submission = ExamSubmission::findOrFail($id);
        
        // Toggle structural visibility flag
        $submission->update(['is_published' => !$submission->is_published]);

        $message = $submission->is_published 
            ? "Grades for {$submission->student_name} have been published live!"
            : "Grades for {$submission->student_name} have been retracted.";

        return redirect()->back()->with('success', $message);
    }
}