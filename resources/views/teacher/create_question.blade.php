<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-5">
    <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
        <h2>📝 Add New Exam Question</h2>
        <hr>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('questions.store') }}" method="POST">
            @csrf

            <input type="hidden" name="exam_id" value="7b45aa67-dd1e-41b8-874c-1ae1737b6b9a">

            <div class="mb-3">
                <label class="form-label">Question Text Content</label>
                <textarea name="content" class="form-control" rows="3" required placeholder="Type your quiz question here..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Question Type</label>
                <select name="type" class="form-select">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="true_false">True / False</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Marks Awarded</label>
                <input type="number" name="marks" step="0.01" class="form-control" value="10.00" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Answer Options Choices</label>
                <input type="text" name="options[]" class="form-control mb-2" placeholder="Option A" required>
                <input type="text" name="options[]" class="form-control mb-2" placeholder="Option B" required>
                <input type="text" name="options[]" class="form-control mb-2" placeholder="Option C" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correct Answer Key</label>
                <input type="text" name="correct_answer[]" class="form-control" placeholder="Must match one option exactly" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">🚀 Save Question to phpMyAdmin</button>
        </form>
    </div>
</body>
</html>