<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment as XAlignment;
use PhpOffice\PhpSpreadsheet\Style\Border as XBorder;

class QualificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Question::query()
                ->with(['answers', 'department'])
                ->select('questions.*');

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('question', 'LIKE', "%{$search}%")
                        ->orWhereHas('answers', function ($subQ) use ($search) {
                            $subQ->where('answer', 'LIKE', "%{$search}%");
                        });
                });
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('answer_type')) {
                $query->where('answer_type', $request->answer_type);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status === 'active' ? 1 : 0);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $sortBy    = $request->get('sort_by', 'id');
            $sortOrder = $request->get('sort_order', 'DESC');
            $query->orderBy($sortBy, $sortOrder);

            $questions = $query->paginate($request->get('per_page', 10))->withQueryString();

            $departments = Department::select('id', 'name')->orderBy('name')->get();

            $pageTitle = 'Setup Q & A';

            return view('admin.qualificationTest.setuptest', compact('questions', 'pageTitle', 'departments'));
        } catch (Exception $e) {
            Log::error('Questions index error: ' . $e->getMessage());
            return back()->with('error', 'Unable to load questions.');
        }
    }

    // ADD QUESTION VIEW
    public function addQuestion()
    {
        try {
            $departments = Department::get();
            $pageTitle   = 'Add Question';
            return view('admin.qualificationTest.add-question', compact('departments', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to open add question page.');
        }
    }

    // STORE QUESTION (AJAX)
    public function storeQuestion(Request $request)
    {
        try {
            // --- BASIC VALIDATION ---
            $request->validate([
                'question'      => 'required|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'answer_type'   => 'required|in:text,single,multi,image,video',
                'status'        => 'required|in:0,1',
            ]);

            $answerType    = $request->answer_type;
            $subAnswerType = null;

            // Resolve effective answer type for options validation
            if (in_array($answerType, ['image', 'video'])) {
                $request->validate([
                    'sub_answer_type' => 'required|in:text,single,multi',
                ], [
                    'sub_answer_type.required' => 'Please select an answer type for this ' . $answerType . ' question.',
                ]);
                $subAnswerType    = $request->sub_answer_type;
                $effectiveType    = $subAnswerType;
            } else {
                $effectiveType = $answerType;
            }

            // --- MEDIA VALIDATION ---
            if ($answerType === 'image') {
                $request->validate([
                    'media_file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                ], [
                    'media_file.required' => 'Please upload an image.',
                    'media_file.image'    => 'File must be an image.',
                    'media_file.max'      => 'Image must be less than 5MB.',
                ]);
            }

            if ($answerType === 'video') {
                $request->validate([
                    'media_file' => 'required|mimes:mp4,mov,webm|max:51200',
                ], [
                    'media_file.required' => 'Please upload a video.',
                    'media_file.max'      => 'Video must be less than 50MB.',
                ]);
            }

            // --- ANSWER OPTIONS VALIDATION ---
            if ($effectiveType === 'text') {
                $request->validate([
                    'text_answer' => 'required|string|max:500',
                ], [
                    'text_answer.required' => 'The answer field is required.',
                ]);
            }

            if ($effectiveType === 'single') {
                $filledOptions = 0;
                if ($request->has('single_options') && is_array($request->single_options)) {
                    foreach ($request->single_options as $opt) {
                        if (!empty(trim($opt))) $filledOptions++;
                    }
                }
                if ($filledOptions < 4) {
                    return response()->json(['success' => false, 'errors' => ['single_options' => 'All 4 answer options are required.']], 422);
                }
                if (!$request->has('correct_option') || $request->correct_option === null) {
                    return response()->json(['success' => false, 'errors' => ['correct_option' => 'Please select exactly one correct answer.']], 422);
                }
            }

            if ($effectiveType === 'multi') {
                $filledOptions = 0;
                if ($request->has('multi_options') && is_array($request->multi_options)) {
                    foreach ($request->multi_options as $opt) {
                        if (!empty(trim($opt))) $filledOptions++;
                    }
                }
                if ($filledOptions < 4) {
                    return response()->json(['success' => false, 'errors' => ['multi_options' => 'All 4 answer options are required.']], 422);
                }
                if (empty($request->correct_options) || count($request->correct_options) < 2) {
                    return response()->json(['success' => false, 'errors' => ['correct_options' => 'Please select a minimum of two correct options.']], 422);
                }
            }

            // --- STORE MEDIA ---
            $mediaPath = null;
            if ($request->hasFile('media_file')) {
                $folder    = $answerType === 'image' ? 'questions/images' : 'questions/videos';
                $mediaPath = $request->file('media_file')->store($folder, 'public');
            }

            // --- CREATE QUESTION ---
            $question = Question::create([
                'question'        => $request->question,
                'department_id'   => $request->department_id,
                'answer_type'     => $answerType,
                'sub_answer_type' => $subAnswerType,
                'media_path'      => $mediaPath,
                'status'          => $request->status,
            ]);

            // --- SAVE ANSWERS ---
            $this->saveAnswers($question, $effectiveType, $request);

            session()->flash('success', 'Question added successfully.');

            return response()->json([
                'success'  => true,
                'message'  => 'Question added successfully',
                'redirect' => route('admin.qualification.setuptest'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Store question error: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => ['general' => 'Failed to add question. Please try again.']], 500);
        }
    }

    // EDIT QUESTION VIEW
    public function editQuestion($id)
    {
        try {
            $question    = Question::with('answers')->findOrFail($id);
            $departments = Department::all();
            $pageTitle   = 'Edit Question';
            return view('admin.qualificationTest.edit-question', compact('question', 'departments', 'pageTitle'));
        } catch (Exception $e) {
            return back()->with('error', 'Unable to load edit page.');
        }
    }

    // UPDATE QUESTION (AJAX)
    public function updateQuestion(Request $request, $id)
    {
        try {
            $request->validate([
                'question'      => 'required|string|max:255',
                'department_id' => 'required|exists:departments,id',
                'answer_type'   => 'required|in:text,single,multi,image,video',
                'status'        => 'required|in:0,1',
            ]);

            $answerType    = $request->answer_type;
            $subAnswerType = null;

            if (in_array($answerType, ['image', 'video'])) {
                $request->validate([
                    'sub_answer_type' => 'required|in:text,single,multi',
                ], [
                    'sub_answer_type.required' => 'Please select an answer type for this ' . $answerType . ' question.',
                ]);
                $subAnswerType = $request->sub_answer_type;
                $effectiveType = $subAnswerType;
            } else {
                $effectiveType = $answerType;
            }

            // Media validation only if new file uploaded
            if ($request->hasFile('media_file')) {
                if ($answerType === 'image') {
                    $request->validate([
                        'media_file' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                    ]);
                }
                if ($answerType === 'video') {
                    $request->validate([
                        'media_file' => 'mimes:mp4,mov,webm|max:51200',
                    ]);
                }
            } else if (in_array($answerType, ['image', 'video'])) {
                // Require media if no existing media
                $question = Question::findOrFail($id);
                if (!$question->media_path) {
                    return response()->json(['success' => false, 'errors' => ['media_file' => 'Please upload a ' . $answerType . '.']], 422);
                }
            }

            if ($effectiveType === 'text') {
                $request->validate(['text_answer' => 'required|string|max:500']);
            }

            if ($effectiveType === 'single') {
                $filledOptions = 0;
                if ($request->has('single_options') && is_array($request->single_options)) {
                    foreach ($request->single_options as $opt) {
                        if (!empty(trim($opt))) $filledOptions++;
                    }
                }
                if ($filledOptions < 4) {
                    return response()->json(['success' => false, 'errors' => ['single_options' => 'All 4 answer options are required.']], 422);
                }
                if (!$request->has('correct_option') || $request->correct_option === null) {
                    return response()->json(['success' => false, 'errors' => ['correct_option' => 'Please select exactly one correct answer.']], 422);
                }
            }

            if ($effectiveType === 'multi') {
                $filledOptions = 0;
                if ($request->has('multi_options') && is_array($request->multi_options)) {
                    foreach ($request->multi_options as $opt) {
                        if (!empty(trim($opt))) $filledOptions++;
                    }
                }
                if ($filledOptions < 4) {
                    return response()->json(['success' => false, 'errors' => ['multi_options' => 'All 4 answer options are required.']], 422);
                }
                if (empty($request->correct_options) || count($request->correct_options) < 2) {
                    return response()->json(['success' => false, 'errors' => ['correct_options' => 'Please select a minimum of two correct options.']], 422);
                }
            }

            $question = Question::findOrFail($id);

            // Handle media upload
            $mediaPath = $question->media_path; // keep existing by default
            if ($request->hasFile('media_file')) {
                // Delete old file
                if ($question->media_path) {
                    Storage::disk('public')->delete($question->media_path);
                }
                $folder    = $answerType === 'image' ? 'questions/images' : 'questions/videos';
                $mediaPath = $request->file('media_file')->store($folder, 'public');
            }

            // If answer type changed from image/video to normal, clear media
            if (!in_array($answerType, ['image', 'video']) && $question->media_path) {
                Storage::disk('public')->delete($question->media_path);
                $mediaPath = null;
            }

            $question->update([
                'question'        => $request->question,
                'department_id'   => $request->department_id,
                'answer_type'     => $answerType,
                'sub_answer_type' => $subAnswerType,
                'media_path'      => $mediaPath,
                'status'          => $request->status,
            ]);

            $question->answers()->delete();
            $this->saveAnswers($question, $effectiveType, $request);
            session()->flash('success', 'Question updated successfully');
            return response()->json([
                'success'  => true,
                'message'  => '',
                'redirect' => route('admin.qualification.setuptest'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            Log::error('Update question error: ' . $e->getMessage());
            return response()->json(['success' => false, 'errors' => ['general' => 'Failed to update question. Please try again.']], 500);
        }
    }

    // DELETE QUESTION
    public function deleteQuestion($id)
    {
        try {
            $question = Question::findOrFail($id);
            if ($question->media_path) {
                Storage::disk('public')->delete($question->media_path);
            }
            $question->answers()->delete();
            $question->delete();
            return redirect()->route('admin.qualification.setuptest')->with('success', 'Question deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete question.');
        }
    }

    // PRIVATE HELPER: Save answers based on effective type
    private function saveAnswers(Question $question, string $effectiveType, Request $request): void
    {
        if ($effectiveType === 'text') {
            Answer::create([
                'question_id' => $question->id,
                'answer'      => $request->text_answer,
                'is_correct'  => 1,
            ]);
        }

        if ($effectiveType === 'single') {
            $correctOption = intval($request->correct_option);
            foreach ($request->single_options as $index => $opt) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer'      => trim($opt),
                    'is_correct'  => ($index === $correctOption) ? 1 : 0,
                ]);
            }
        }

        if ($effectiveType === 'multi') {
            $correctOptions = array_map('intval', $request->correct_options);
            foreach ($request->multi_options as $index => $opt) {
                Answer::create([
                    'question_id' => $question->id,
                    'answer'      => trim($opt),
                    'is_correct'  => in_array($index, $correctOptions) ? 1 : 0,
                ]);
            }
        }
    }

    public function bulkImportView()
    {
        $pageTitle   = 'Bulk Import Questions';
        $departments = Department::orderBy('name')->get();
        return view('admin.qualificationTest.bulk-import', compact('pageTitle', 'departments'));
    }

    // ──────────────────────────────────────────────
    //  2. PROCESS UPLOAD
    // ──────────────────────────────────────────────
    public function processBulkImport(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'import_file.required' => 'Please upload an Excel file.',
            'import_file.mimes'    => 'Only .xlsx or .xls files are accepted.',
            'import_file.max'      => 'File must be under 10 MB.',
        ]);

        try {
            $path        = $request->file('import_file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, true);

            // Build header → column-letter map from row 1
            $headerRow = array_shift($rows);
            $headerMap = [];
            foreach ($headerRow as $col => $val) {
                if (!empty(trim((string)$val))) {
                    $headerMap[strtolower(trim((string)$val))] = $col;
                }
            }

            // Required header check
            $requiredHeaders = ['question', 'department_id', 'answer_type', 'status'];
            $missing = array_diff($requiredHeaders, array_keys($headerMap));
            if (!empty($missing)) {
                return back()->with('error', 'Missing required columns: ' . implode(', ', $missing));
            }

            $validDeptIds = Department::pluck('id')->toArray();
            $imported  = 0;
            $failed    = [];
            $rowNumber = 2;

            foreach ($rows as $row) {
                // Skip blank rows
                $allEmpty = true;
                foreach ($row as $cell) {
                    if (!empty(trim((string)$cell))) {
                        $allEmpty = false;
                        break;
                    }
                }
                if ($allEmpty) {
                    $rowNumber++;
                    continue;
                }

                $get = fn($key) => isset($headerMap[$key])
                    ? trim((string)($row[$headerMap[$key]] ?? ''))
                    : '';

                $question      = $get('question');
                $departmentId  = $get('department_id');
                $answerType    = strtolower($get('answer_type'));
                $subAnswerType = strtolower($get('sub_answer_type'));
                $status        = $get('status');
                $mediaUrl      = $get('media_url');
                $textAnswer    = $get('text_answer');
                $option1       = $get('option_1');
                $option2       = $get('option_2');
                $option3       = $get('option_3');
                $option4       = $get('option_4');
                $correctRaw    = $get('correct_options');

                $errors = [];

                // ── Question text ──
                if (empty($question)) {
                    $errors[] = 'Question text is required.';
                } elseif (mb_strlen($question) > 255) {
                    $errors[] = 'Question text exceeds 255 characters (got ' . mb_strlen($question) . ').';
                }

                // ── Department ──
                if (empty($departmentId)) {
                    $errors[] = 'department_id is required.';
                } elseif (!is_numeric($departmentId) || !in_array((int)$departmentId, $validDeptIds)) {
                    $errors[] = "department_id '{$departmentId}' does not exist in the database.";
                }

                // ── Answer type ──
                $validTypes = ['text', 'single', 'multi', 'image', 'video'];
                if (empty($answerType)) {
                    $errors[] = 'answer_type is required.';
                } elseif (!in_array($answerType, $validTypes)) {
                    $errors[] = "answer_type '{$answerType}' is invalid. Allowed: " . implode(', ', $validTypes) . '.';
                }

                // ── Status ──
                if ($status === '') {
                    $errors[] = 'status is required (1 = Active, 0 = Inactive).';
                } elseif (!in_array($status, ['0', '1'])) {
                    $errors[] = "status '{$status}' is invalid. Use 1 or 0.";
                }

                // ── Effective type (resolve sub for image/video) ──
                $effectiveType = $answerType;
                if (in_array($answerType, ['image', 'video'])) {
                    $validSub = ['text', 'single', 'multi'];
                    if (empty($subAnswerType)) {
                        $errors[] = 'sub_answer_type is required when answer_type is image or video.';
                    } elseif (!in_array($subAnswerType, $validSub)) {
                        $errors[] = "sub_answer_type '{$subAnswerType}' is invalid. Allowed: text, single, multi.";
                    } else {
                        $effectiveType = $subAnswerType;
                    }

                    // ── Media URL validation ──
                    if (empty($mediaUrl)) {
                        $errors[] = "media_url is required for {$answerType} questions. Provide a public URL.";
                    } elseif (!filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
                        $errors[] = "media_url '{$mediaUrl}' is not a valid URL.";
                    } else {
                        // Check URL reachability with a HEAD request (lightweight)
                        $headers = @get_headers($mediaUrl, 1);
                        if (!$headers) {
                            $errors[] = "media_url could not be reached. Ensure it is publicly accessible.";
                        } else {
                            $httpStatus = is_array($headers[0]) ? end($headers[0]) : $headers[0];
                            if (!preg_match('/200/', $httpStatus)) {
                                $errors[] = "media_url returned HTTP status: {$httpStatus}. File must be publicly accessible (HTTP 200).";
                            } else {
                                // Validate MIME / extension from URL
                                $urlPath  = parse_url($mediaUrl, PHP_URL_PATH) ?? '';
                                $ext      = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));
                                if ($answerType === 'image') {
                                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                    if (!empty($ext) && !in_array($ext, $allowedExts)) {
                                        $errors[] = "Image URL has unsupported extension '{$ext}'. Allowed: " . implode(', ', $allowedExts) . '.';
                                    }
                                } elseif ($answerType === 'video') {
                                    $allowedExts = ['mp4', 'mov', 'webm'];
                                    if (!empty($ext) && !in_array($ext, $allowedExts)) {
                                        $errors[] = "Video URL has unsupported extension '{$ext}'. Allowed: " . implode(', ', $allowedExts) . '.';
                                    }
                                }
                            }
                        }
                    }
                }

                // ── Text answer ──
                if ($effectiveType === 'text' && empty($errors)) {
                    if (empty($textAnswer)) {
                        $errors[] = 'text_answer is required when answer type is text.';
                    } elseif (mb_strlen($textAnswer) > 500) {
                        $errors[] = 'text_answer exceeds 500 characters (got ' . mb_strlen($textAnswer) . ').';
                    }
                }

                // ── Single / Multi options ──
                $correctNums = [];
                if (in_array($effectiveType, ['single', 'multi']) && empty($errors)) {
                    $options      = [$option1, $option2, $option3, $option4];
                    $filledOptions = count(array_filter($options, fn($o) => $o !== ''));

                    if ($filledOptions < 4) {
                        $errors[] = "All 4 options are required for {$effectiveType} questions. Got {$filledOptions}.";
                    }

                    if (!empty($correctRaw)) {
                        foreach (explode(',', $correctRaw) as $cn) {
                            $cn = (int)trim($cn);
                            if ($cn >= 1 && $cn <= 4) {
                                $correctNums[] = $cn;
                            } else {
                                $errors[] = "correct_options value '{$cn}' is out of range. Must be 1–4.";
                            }
                        }
                        $correctNums = array_values(array_unique($correctNums));
                    }

                    if ($effectiveType === 'single') {
                        if (empty($correctRaw)) {
                            $errors[] = 'correct_options is required for single select (provide one number 1–4).';
                        } elseif (count($correctNums) !== 1) {
                            $errors[] = 'Single select requires exactly ONE correct option. Got ' . count($correctNums) . '.';
                        }
                    }

                    if ($effectiveType === 'multi') {
                        if (empty($correctRaw)) {
                            $errors[] = 'correct_options is required for multi select (comma-separated, at least 2).';
                        } elseif (count($correctNums) < 2) {
                            $errors[] = 'Multi select requires at least TWO correct options. Got ' . count($correctNums) . '.';
                        }
                    }
                }

                // ═══════════════════════════════
                //  SAVE  or  SKIP
                // ═══════════════════════════════
                if (!empty($errors)) {
                    $failed[] = [
                        'row'           => $rowNumber,
                        'question'      => mb_substr($question ?: '(empty)', 0, 80),
                        'answer_type'   => $answerType,
                        'department_id' => $departmentId,
                        'status'        => $status,
                        'reasons'       => implode(' | ', $errors),
                    ];
                } else {
                    // Download and store media file if image/video
                    $mediaPath = null;
                    if (in_array($answerType, ['image', 'video']) && !empty($mediaUrl)) {
                        $mediaPath = $this->downloadAndStoreMedia($mediaUrl, $answerType, $errors);
                        if (!empty($errors)) {
                            // Download failed – skip this row
                            $failed[] = [
                                'row'           => $rowNumber,
                                'question'      => mb_substr($question, 0, 80),
                                'answer_type'   => $answerType,
                                'department_id' => $departmentId,
                                'status'        => $status,
                                'reasons'       => implode(' | ', $errors),
                            ];
                            $rowNumber++;
                            continue;
                        }
                    }

                    // Create question
                    $q = Question::create([
                        'question'        => $question,
                        'department_id'   => (int)$departmentId,
                        'answer_type'     => $answerType,
                        'sub_answer_type' => in_array($answerType, ['image', 'video']) ? $subAnswerType : null,
                        'media_path'      => $mediaPath,
                        'status'          => (int)$status,
                    ]);

                    // Save answers
                    if ($effectiveType === 'text') {
                        Answer::create([
                            'question_id' => $q->id,
                            'answer'      => $textAnswer,
                            'is_correct'  => 1,
                        ]);
                    } elseif (in_array($effectiveType, ['single', 'multi'])) {
                        $optsList = [$option1, $option2, $option3, $option4];
                        foreach ($optsList as $idx => $opt) {
                            Answer::create([
                                'question_id' => $q->id,
                                'answer'      => $opt,
                                'is_correct'  => in_array($idx + 1, $correctNums) ? 1 : 0,
                            ]);
                        }
                    }

                    $imported++;
                }

                $rowNumber++;
            }

            // Generate failed rows Excel
            $failedFilename = null;
            if (!empty($failed)) {
                $failedFilename = $this->generateFailedExcel($failed);
            }

            return back()
                ->with('import_success', $imported)
                ->with('import_failed', count($failed))
                ->with('failed_file', $failedFilename);
        } catch (\Exception $e) {
            Log::error('Bulk import error: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    //  3. DOWNLOAD  (sample template + failed report)
    // ──────────────────────────────────────────────
    public function bulkImportDownload(Request $request)
    {
        $file = $request->get('file', '');

        // ── Sample template ──
        if ($file === 'sample') {
            $samplePath = storage_path('app/private/bulk_imports/sample_template.xlsx');

            // Generate on-the-fly if it doesn't exist
            if (!File::exists($samplePath)) {
                $this->generateSampleTemplate($samplePath);
            }

            return response()->download($samplePath, 'bulk_questions_sample_template.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        // ── Failed rows report ──
        if (!preg_match('/^failed_imports_\d{8}_\d{6}\.xlsx$/', basename($file))) {
            abort(404, 'File not found.');
        }

        $path = storage_path('app/private/bulk_imports/' . basename($file));
        if (!File::exists($path)) {
            abort(404, 'Report file has expired or does not exist.');
        }

        return response()->download($path, basename($file), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ──────────────────────────────────────────────
    //  PRIVATE: Download media from URL and store
    // ──────────────────────────────────────────────
    private function downloadAndStoreMedia(string $url, string $type, array &$errors): ?string
    {
        try {
            $ctx = stream_context_create([
                'http' => [
                    'timeout'       => 30,
                    'ignore_errors' => false,
                    'header'        => "User-Agent: Mozilla/5.0\r\n",
                ],
                'ssl' => ['verify_peer' => false],
            ]);

            $content = @file_get_contents($url, false, $ctx);
            if ($content === false) {
                $errors[] = "Failed to download media from URL: {$url}";
                return null;
            }

            $maxBytes = $type === 'image' ? 5 * 1024 * 1024 : 50 * 1024 * 1024;
            if (strlen($content) > $maxBytes) {
                $sizeMb  = round(strlen($content) / 1024 / 1024, 1);
                $limitMb = $type === 'image' ? '5' : '50';
                $errors[] = "Media file is too large ({$sizeMb} MB). Limit is {$limitMb} MB.";
                return null;
            }

            // Detect MIME
            $finfo    = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($content);

            if ($type === 'image') {
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                $mimeExtMap   = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors[] = "Downloaded file MIME type '{$mimeType}' is not an allowed image type.";
                    return null;
                }
                $ext    = $mimeExtMap[$mimeType] ?? 'jpg';
                $folder = 'questions/images';
            } else {
                $allowedMimes = ['video/mp4', 'video/quicktime', 'video/webm'];
                $mimeExtMap   = ['video/mp4' => 'mp4', 'video/quicktime' => 'mov', 'video/webm' => 'webm'];
                if (!in_array($mimeType, $allowedMimes)) {
                    $errors[] = "Downloaded file MIME type '{$mimeType}' is not an allowed video type.";
                    return null;
                }
                $ext    = $mimeExtMap[$mimeType] ?? 'mp4';
                $folder = 'questions/videos';
            }

            $filename  = uniqid('import_', true) . '.' . $ext;
            $storePath = $folder . '/' . $filename;
            $fullPath  = storage_path('app/public/' . $storePath);

            @mkdir(dirname($fullPath), 0755, true);
            file_put_contents($fullPath, $content);

            return $storePath;
        } catch (\Exception $e) {
            $errors[] = 'Media download error: ' . $e->getMessage();
            return null;
        }
    }

    // ──────────────────────────────────────────────
    //  PRIVATE: Generate failed-rows Excel report
    // ──────────────────────────────────────────────
    private function generateSampleTemplate(string $savePath): void
    {
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Instructions ──
        $ins = $spreadsheet->getActiveSheet();
        $ins->setTitle('Instructions');
        $ins->getColumnDimension('A')->setWidth(22);
        $ins->getColumnDimension('B')->setWidth(75);

        $ins->setCellValue('A1', 'Bulk Question Import - Format Guide');
        $ins->mergeCells('A1:B1');
        $ins->getRowDimension(1)->setRowHeight(32);

        $guide = [
            ['COLUMN',          'DESCRIPTION'],
            ['question',        'Required. The question text. Max 255 characters.'],
            ['department_id',   'Required. Numeric ID of existing department.'],
            ['answer_type',     'Required. One of: text | single | multi | image | video'],
            ['sub_answer_type', 'Required ONLY for image/video. One of: text | single | multi'],
            ['status',          '1 = Active, 0 = Inactive'],
            ['media_url',       'Required for image/video. Publicly accessible URL.'],
            ['text_answer',     'Required when effective type = text. Max 500 chars.'],
            ['option_1',        'Required for single/multi. Must fill all 4 options.'],
            ['option_2',        ''],
            ['option_3',        ''],
            ['option_4',        ''],
            ['correct_options', 'Single: one number e.g. 2  |  Multi: comma-separated e.g. 1,3'],
        ];

        foreach ($guide as $ri => $rowData) {
            $r = $ri + 2;
            $ins->setCellValue('A' . $r, $rowData[0]);
            $ins->setCellValue('B' . $r, $rowData[1]);
        }

        // ── Sheet 2: Questions ──
        $ws = $spreadsheet->createSheet();
        $ws->setTitle('Questions');

        $headers = [
            'question',
            'department_id',
            'answer_type',
            'sub_answer_type',
            'status',
            'media_url',
            'text_answer',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'correct_options'
        ];
        $widths = [42, 14, 14, 16, 8, 40, 30, 20, 20, 20, 20, 16];

        foreach ($headers as $ci => $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
            $ws->setCellValue($col . '1', $h);
            $ws->getColumnDimension($col)->setWidth($widths[$ci]);
        }
        $ws->getRowDimension(1)->setRowHeight(28);
        $ws->freezePane('A2');

        $IMG = 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/PNG_transparency_demonstration_1.png/240px-PNG_transparency_demonstration_1.png';
        $VID = 'https://www.w3schools.com/html/mov_bbb.mp4';

        $sampleData = [
            ["What is the capital of France?", 1, "text", "", 1, "", "Paris", "", "", "", "", ""],
            ["Who invented the telephone?", 1, "text", "", 0, "", "Alexander Graham Bell", "", "", "", "", ""],
            ["What does HTML stand for?", 2, "text", "", 1, "", "HyperText Markup Language", "", "", "", "", ""],
            ["What is the boiling point of water in Celsius?", 1, "text", "", 1, "", "100", "", "", "", "", ""],
            ["What is the chemical symbol for Gold?", 1, "text", "", 1, "", "Au", "", "", "", "", ""],
            ["Which planet is closest to the Sun?", 1, "single", "", 1, "", "", "Mercury", "Venus", "Earth", "Mars", "1"],
            ["Which data structure uses FIFO?", 2, "single", "", 1, "", "", "Stack", "Queue", "Tree", "Graph", "2"],
            ["What is the output of 2 to the power 10?", 2, "single", "", 1, "", "", "512", "1024", "2048", "256", "2"],
            ["Which HTTP method updates a resource?", 2, "single", "", 1, "", "", "GET", "POST", "PUT", "DELETE", "3"],
            ["What does SQL stand for?", 2, "single", "", 1, "", "", "Structured Query Language", "Simple Query", "Standard Query Logic", "System Query Language", "1"],
            ["What is the default port for HTTPS?", 2, "single", "", 1, "", "", "80", "8080", "443", "22", "3"],
            ["What does CPU stand for?", 2, "single", "", 1, "", "", "Central Processing Unit", "Computer Processing Unit", "Core Processing Unit", "Central Program Unit", "1"],
            ["Which sorting is fastest on average?", 2, "single", "", 1, "", "", "Bubble Sort", "Insertion Sort", "Quick Sort", "Selection Sort", "3"],
            ["Which are programming languages?", 2, "multi", "", 1, "", "", "Python", "HTML", "JavaScript", "CSS", "1,3"],
            ["Which are NoSQL databases?", 2, "multi", "", 1, "", "", "MongoDB", "MySQL", "Redis", "PostgreSQL", "1,3"],
            ["Which are valid HTTP 2xx codes?", 2, "multi", "", 1, "", "", "200", "301", "201", "404", "1,3"],
            ["Which are JavaScript frameworks?", 2, "multi", "", 1, "", "", "React", "Django", "Vue", "Laravel", "1,3"],
            ["Select all primary colors:", 1, "multi", "", 1, "", "", "Red", "Green", "Blue", "Purple", "1,2,3"],
            ["Which planets have rings?", 1, "multi", "", 1, "", "", "Saturn", "Jupiter", "Mars", "Uranus", "1,2,4"],
            ["Which are OOP principles?", 2, "multi", "", 1, "", "", "Encapsulation", "Compilation", "Inheritance", "Polymorphism", "1,3,4"],
            ["Look at the image - what does it show?", 1, "image", "single", 1, $IMG, "", "Option A", "Option B", "Option C", "Option D", "1"],
            ["Identify the content in this image:", 1, "image", "multi", 1, $IMG, "", "Choice 1", "Choice 2", "Choice 3", "Choice 4", "1,2"],
            ["Watch the video - what animal appears?", 1, "video", "single", 1, $VID, "", "Rabbit", "Bear", "Elephant", "Dog", "3"],
            ["Watch and select all that apply:", 1, "video", "multi", 1, $VID, "", "Scene A", "Scene B", "Scene C", "Scene D", "1,3"],
            // intentional failures
            ["", 1, "text", "", 1, "", "Some answer", "", "", "", "", ""],
            ["What is the speed of light?", 9999, "text", "", 1, "", "299792458 m/s", "", "", "", "", ""],
            ["What is gravity?", 1, "checkbox", "", 1, "", "Force", "", "", "", "", ""],
            ["What is the formula for water?", 1, "text", "", 1, "", "", "", "", "", "", ""],
            ["Which continent is Egypt in?", 1, "single", "", 1, "", "", "Africa", "Asia", "Europe", "", "1"],
            ["What is 7 multiplied by 8?", 1, "single", "", 1, "", "", "54", "56", "58", "60", ""],
            ["Select programming paradigms:", 2, "multi", "", 1, "", "", "OOP", "Functional", "Procedural", "Visual", "1"],
            ["Which are web browsers?", 1, "multi", "", 1, "", "", "Chrome", "Firefox", "", "", "1,2"],
            ["What is RAM?", 2, "text", "", 5, "", "Random Access Memory", "", "", "", "", ""],
            ["Look at the image carefully:", 1, "image", "single", 1, "", "", "Opt A", "Opt B", "Opt C", "Opt D", "1"],
        ];

        foreach ($sampleData as $ri => $rowData) {
            $r = $ri + 2;
            foreach ($rowData as $ci => $val) {
                $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($ci + 1);
                $ws->setCellValue($col . $r, $val);
            }
            $ws->getRowDimension($r)->setRowHeight(20);
        }

        // Legend
        $lr = count($sampleData) + 3;
        $ws->setCellValue('A' . $lr, 'Legend: White/grey = valid rows | Yellow = intentional failures for testing');

        $spreadsheet->setActiveSheetIndex(1);

        if (!is_dir(dirname($savePath))) {
            mkdir(dirname($savePath), 0755, true);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($savePath);
    }

    private function generateFailedExcel(array $failed): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Failed Rows');

        $sheet->setCellValue('A1', 'Failed Import Rows - Generated: ' . now()->format('d M Y, H:i:s'));
        $sheet->mergeCells('A1:F1');
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(50);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(8);
        $sheet->getColumnDimension('F')->setWidth(72);

        $sheet->setCellValue('A2', 'Row #');
        $sheet->setCellValue('B2', 'Question (truncated)');
        $sheet->setCellValue('C2', 'Answer Type');
        $sheet->setCellValue('D2', 'Department ID');
        $sheet->setCellValue('E2', 'Status');
        $sheet->setCellValue('F2', 'Failure Reason(s)');
        $sheet->getRowDimension(2)->setRowHeight(24);
        $sheet->freezePane('A3');

        foreach ($failed as $i => $row) {
            $r = $i + 3;
            $sheet->setCellValue('A' . $r, $row['row']);
            $sheet->setCellValue('B' . $r, $row['question']);
            $sheet->setCellValue('C' . $r, $row['answer_type']);
            $sheet->setCellValue('D' . $r, $row['department_id']);
            $sheet->setCellValue('E' . $r, $row['status']);
            $sheet->setCellValue('F' . $r, $row['reasons']);
            $sheet->getRowDimension($r)->setRowHeight(30);
        }

        $sr = count($failed) + 4;
        $sheet->mergeCells('A' . $sr . ':F' . $sr);
        $sheet->setCellValue('A' . $sr, 'Total failed: ' . count($failed) . ' | Fix the issues above and re-import only these rows.');

        $dir = storage_path('app/private/bulk_imports');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = 'failed_imports_' . now()->format('Ymd_His') . '.xlsx';
        $filePath = $dir . '/' . $filename;

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($filePath);

        return $filename;
    }
}
