@extends('layout.admin.app')

@section('content')
    <div class="content-card">

        <div class="div-main-haeds">
            <h2 class="page-title">Add Test</h2>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form class="add-ques" action="{{ route('admin.test.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <div class="col-12 col-md-6 top-2">
                    <label>Test Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name')}}"
                        class="custom-input form-control" placeholder="Enter test name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Test Fee <span class="text-danger">*</span></label>
                    <input type="number" name="fees" value="{{ old('fees') }}"
                        class="custom-input form-control" placeholder="Enter test fee" step="1" min="0">
                    @error('fees')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <div class="col-12 col-md-12 top-2">
                    <label>Test Timing <span class="text-danger">*</span></label>
                    <select name="timing" class="custom-input form-control">
                        <option value="">Select Time</option>
                        @foreach ([60, 75, 90] as $t)
                            <option value="{{ $t }}" {{ old('timing') == $t ? 'selected' : '' }}>
                                {{ $t }} Minutes
                            </option>
                        @endforeach
                    </select>
                    @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               <div class="col-12"><hr><h4 class="test-config mt-3 mb-4">Text Questions Configuration</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>Marks per Text Question <span class="text-danger">*</span></label>
                    <input type="number" name="text_marks" value="{{old('text_marks') }}"
                        class="custom-input form-control" placeholder="Enter Marks per question" step="1" min="0">
                    @error('text_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Text Question per Department<span class="text-danger">*</span></label>
                    <input type="number" name="text_q_per_dept" value="{{old('text_q_per_dept') }}"
                        class="custom-input form-control" placeholder="Enter no. of text type questions per department" step="1" min="0">
                    @error('text_q_per_dept')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               <div class="col-12"><hr><h4 class=" test-config mt-3 mb-3">Single Select Questions Configuration</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>Marks per Single Select Question <span class="text-danger">*</span></label>
                    <input type="number" name="single_select_marks" value="{{old('single_select_marks') }}"
                        class="custom-input form-control" placeholder="Enter Marks per question" step="1" min="0">
                    @error('single_select_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Single Select Question per Department<span class="text-danger">*</span></label>
                    <input type="number" name="single_select_q_per_dept" value="{{old('single_select_q_per_dept') }}"
                        class="custom-input form-control" placeholder="Enter no. of single select questions per department" step="1" min="0">
                    @error('single_select_q_per_dept')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               
                <div class="col-12"><hr><h4 class="test-config mt-3 mb-4">Multi Select Questions Configuration</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>Marks per Multi Select Question <span class="text-danger">*</span></label>
                    <input type="number" name="multi_select_marks" value="{{old('multi_select_marks') }}"
                        class="custom-input form-control" placeholder="Enter Marks per question" step="1" min="0">
                    @error('multi_select_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Multi Select Question per Department<span class="text-danger">*</span></label>
                    <input type="number" name="multi_select_q_per_dept" value="{{old('multi_select_q_per_dept') }}"
                        class="custom-input form-control" placeholder="Enter no. of multi select questions per department" step="1" min="0">
                    @error('multi_select_q_per_dept')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               
                <div class="col-12"><hr><h4 class="test-config mt-3 mb-4">Image Questions Configuration</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>Marks per Image Question <span class="text-danger">*</span></label>
                    <input type="number" name="image_marks" value="{{old('image_marks') }}"
                        class="custom-input form-control" placeholder="Enter Marks per question" step="1" min="0">
                    @error('image_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Image Question per Department<span class="text-danger">*</span></label>
                    <input type="number" name="image_q_per_dept" value="{{old('image_q_per_dept') }}"
                        class="custom-input form-control" placeholder="Enter no. of image questions per department" step="1" min="0">
                    @error('image_q_per_dept')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

               
                <div class="col-12"><hr><h4 class="test-config mt-3 mb-4">Video Questions Configuration</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>Marks per Video Question <span class="text-danger">*</span></label>
                    <input type="number" name="video_marks" value="{{old('video_marks') }}"
                        class="custom-input form-control" placeholder="Enter Marks per question" step="1" min="0">
                    @error('video_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Video Question per Department<span class="text-danger">*</span></label>
                    <input type="number" name="video_q_per_dept" value="{{old('video_q_per_dept') }}"
                        class="custom-input form-control" placeholder="Enter no. of video questions per department" step="1" min="0">
                    @error('video_q_per_dept')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12"><hr><h4 class="test-config mt-3 mb-4">Test Summary (Auto-Calculated)</h4></div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Departments <span class="text-danger">*</span></label>
                    <input type="number" id="no_of_departments" name="no_of_departments"
                        value="{{ old('no_of_departments', $departmentCount)}}" class="custom-input form-control"
                        placeholder="No. of Departments" readonly step="1" min="0">
                    @error('no_of_departments')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Question per Department <span class="text-danger">*</span></label>
                    <input type="number" id="question_per_dept" name="question_per_department"
                        value="{{ old('question_per_department') }}" class="custom-input form-control"
                        placeholder="Auto-calculated" readonly step="1" min="0">
                    @error('question_per_department')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Total No. of Questions <span class="text-danger">*</span></label>
                    <input type="number" id="total_questions" name="total_question"
                        value="{{ old('total_question') }}" class="custom-input form-control" readonly step="1" min="0">
                    @error('total_question')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Total Marks <span class="text-danger">*</span></label>
                    <input type="number" id="total_marks" name="total_marks" value="{{ old('total_marks') }}"
                        class="custom-input form-control" readonly step="1" min="0">
                    @error('total_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-6 col-md-6 top-2">
                    <label>Passing Marks <span class="text-danger">*</span></label>
                    <input type="number" name="passing_marks" value="{{ old('passing_marks') }}"
                        class="custom-input form-control" placeholder="Enter passing marks" step="1" min="0">
                    @error('passing_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                  <div class="col-6 col-md-6 top-2">
                    <label>Status <span class="text-danger">*</span></label>
                    <select name="status" class="custom-input form-control">
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <button type="submit" class="btn btn-submit-form">Save Test</button>
        </form>

    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
$(document).ready(function() {
    
    // Prevent non-integer input - user can't even type decimal points or negative signs
    $('input[type="number"]').on('keypress', function(e) {
        // Allow: backspace, delete, tab, escape, enter
        if (e.which === 8 || e.which === 9 || e.which === 27 || e.which === 13) {
            return;
        }
        // Allow only digits (0-9)
        if (e.which < 48 || e.which > 57) {
            e.preventDefault();
        }
    });
    
    // Prevent pasting non-integer values
    $('input[type="number"]').on('paste', function(e) {
        e.preventDefault();
        const pastedData = (e.originalEvent || e).clipboardData.getData('text/plain');
        // Only allow if pasted data is pure integer
        if (/^\d+$/.test(pastedData)) {
            $(this).val(pastedData);
            calculateTotals();
        }
    });
    
    // Prevent input events with non-integers (for safety)
    $('input[type="number"]').on('input', function() {
        // Remove any non-digit characters
        this.value = this.value.replace(/[^\d]/g, '');
    });
    
    // Real-time calculation function
    function calculateTotals() {
        // Get all input values (use parseInt for integers)
        const textMarks = parseInt($('input[name="text_marks"]').val()) || 0;
        const textQPerDept = parseInt($('input[name="text_q_per_dept"]').val()) || 0;
        
        const singleMarks = parseInt($('input[name="single_select_marks"]').val()) || 0;
        const singleQPerDept = parseInt($('input[name="single_select_q_per_dept"]').val()) || 0;
        
        const multiMarks = parseInt($('input[name="multi_select_marks"]').val()) || 0;
        const multiQPerDept = parseInt($('input[name="multi_select_q_per_dept"]').val()) || 0;
        
        const imageMarks = parseInt($('input[name="image_marks"]').val()) || 0;
        const imageQPerDept = parseInt($('input[name="image_q_per_dept"]').val()) || 0;
        
        const videoMarks = parseInt($('input[name="video_marks"]').val()) || 0;
        const videoQPerDept = parseInt($('input[name="video_q_per_dept"]').val()) || 0;
        
        const totalDepartments = parseInt($('#no_of_departments').val()) || 0;
        
        // Calculate total questions per department (sum of all question types)
        const questionsPerDept = textQPerDept + singleQPerDept + multiQPerDept + imageQPerDept + videoQPerDept;
        $('#question_per_dept').val(questionsPerDept);
        
        // Calculate total questions (questions per dept * total departments)
        const totalQuestions = questionsPerDept * totalDepartments;
        $('#total_questions').val(totalQuestions);
        
        // Calculate total marks
        // (marks per question type * questions per dept for that type) * total departments
        const totalMarks = (
            (textMarks * textQPerDept) +
            (singleMarks * singleQPerDept) +
            (multiMarks * multiQPerDept) +
            (imageMarks * imageQPerDept) +
            (videoMarks * videoQPerDept)
        ) * totalDepartments;
        
        $('#total_marks').val(totalMarks);
        
        // Update passing marks validation
        const passingMarksInput = $('input[name="passing_marks"]');
        passingMarksInput.attr('max', totalMarks);
        
        // Validate passing marks if already entered
        const currentPassingMarks = parseInt(passingMarksInput.val()) || 0;
        if (currentPassingMarks >= totalMarks && totalMarks > 0) {
            passingMarksInput.siblings('.text-danger').not('.error').remove();
            passingMarksInput.after('<span class="text-danger dynamic-error">Passing marks must be less than total marks (' + totalMarks + ')</span>');
        } else {
            passingMarksInput.siblings('.dynamic-error').remove();
        }
    }
    
    // Trigger calculation on any input change (only for question/marks fields)
    $('input[name="text_marks"], input[name="text_q_per_dept"], input[name="single_select_marks"], input[name="single_select_q_per_dept"], input[name="multi_select_marks"], input[name="multi_select_q_per_dept"], input[name="image_marks"], input[name="image_q_per_dept"], input[name="video_marks"], input[name="video_q_per_dept"]').on('input', function() {
        calculateTotals();
    });
    
    // Initial calculation on page load
    calculateTotals();
    
    // jQuery Validation with integer rules
    $.validator.addMethod("integer", function(value, element) {
        return this.optional(element) || /^\d+$/.test(value);
    }, "Please enter a valid integer");
    
    $('.add-ques').validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                maxlength: 255
            },
            fees: {
                required: true,
                integer: true,
                min: 0
            },
            timing: {
                required: true
            },
            text_marks: {
                required: true,
                integer: true,
                min: 0
            },
            text_q_per_dept: {
                required: true,
                integer: true,
                min: 0
            },
            single_select_marks: {
                required: true,
                integer: true,
                min: 0
            },
            single_select_q_per_dept: {
                required: true,
                integer: true,
                min: 0
            },
            multi_select_marks: {
                required: true,
                integer: true,
                min: 0
            },
            multi_select_q_per_dept: {
                required: true,
                integer: true,
                min: 0
            },
            image_marks: {
                required: true,
                integer: true,
                min: 0
            },
            image_q_per_dept: {
                required: true,
                integer: true,
                min: 0
            },
            video_marks: {
                required: true,
                integer: true,
                min: 0
            },
            video_q_per_dept: {
                required: true,
                integer: true,
                min: 0
            },
            no_of_departments: {
                required: true,
                integer: true,
                min: 1
            },
            question_per_department: {
                required: true,
                integer: true,
                min: 1
            },
            total_question: {
                required: true,
                integer: true,
                min: 1
            },
            total_marks: {
                required: true,
                integer: true,
                min: 1
            },
            passing_marks: {
                required: true,
                integer: true,
                min: 0,
                max: function() {
                    return parseInt($('#total_marks').val()) || 0;
                }
            }
        },
        messages: {
            name: {
                required: "Please enter test name",
                minlength: "Test name must be at least 3 characters",
                maxlength: "Test name cannot exceed 255 characters"
            },
            fees: {
                required: "Please enter test fee",
                integer: "Please enter a valid integer",
                min: "Test fee cannot be negative"
            },
            timing: {
                required: "Please select test timing"
            },
            text_marks: {
                required: "Please enter marks for text questions",
                integer: "Please enter a valid integer",
                min: "Marks cannot be negative"
            },
            text_q_per_dept: {
                required: "Please enter number of text questions per department",
                integer: "Please enter a valid integer",
                min: "Number cannot be negative"
            },
            single_select_marks: {
                required: "Please enter marks for single select questions",
                integer: "Please enter a valid integer",
                min: "Marks cannot be negative"
            },
            single_select_q_per_dept: {
                required: "Please enter number of single select questions per department",
                integer: "Please enter a valid integer",
                min: "Number cannot be negative"
            },
            multi_select_marks: {
                required: "Please enter marks for multi select questions",
                integer: "Please enter a valid integer",
                min: "Marks cannot be negative"
            },
            multi_select_q_per_dept: {
                required: "Please enter number of multi select questions per department",
                integer: "Please enter a valid integer",
                min: "Number cannot be negative"
            },
            image_marks: {
                required: "Please enter marks for image questions",
                integer: "Please enter a valid integer",
                min: "Marks cannot be negative"
            },
            image_q_per_dept: {
                required: "Please enter number of image questions per department",
                integer: "Please enter a valid integer",
                min: "Number cannot be negative"
            },
            video_marks: {
                required: "Please enter marks for video questions",
                integer: "Please enter a valid integer",
                min: "Marks cannot be negative"
            },
            video_q_per_dept: {
                required: "Please enter number of video questions per department",
                integer: "Please enter a valid integer",
                min: "Number cannot be negative"
            },
            no_of_departments: {
                required: "Number of departments is required",
                integer: "Please enter a valid integer",
                min: "At least 1 department is required"
            },
            question_per_department: {
                required: "Questions per department is required",
                integer: "Please enter a valid integer",
                min: "At least 1 question per department is required"
            },
            total_question: {
                required: "Total questions is required",
                integer: "Please enter a valid integer",
                min: "At least 1 question is required"
            },
            total_marks: {
                required: "Total marks is required",
                integer: "Please enter a valid integer",
                min: "At least 1 mark is required"
            },
            passing_marks: {
                required: "Please enter passing marks",
                integer: "Please enter a valid integer",
                min: "Passing marks cannot be negative",
                max: "Passing marks must be less than total marks"
            }
        },
        errorElement: 'span',
        errorClass: 'text-danger error',
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        },
        
    });
});
</script>
@endsection