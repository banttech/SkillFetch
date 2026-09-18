@extends('layout.admin.app')

@section('content')
    <div class="content-card add-quests">

        <div class="div-main-haeds">
            <h2 class="page-title">Add Question</h2>
        </div>

        {{-- Alert Placeholders --}}
        <div id="ajax-success" class="alert alert-success" style="display:none;"></div>
        <div id="ajax-error"   class="alert alert-danger"  style="display:none;"></div>

        <form id="questionForm" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">

                {{-- Question --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Enter the Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" placeholder="Enter your question here"
                        class="custom-input form-control" id="question">
                    <span class="text-danger field-error" id="err-question"></span>
                </div>

                {{-- Department --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Select Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="custom-input form-control" id="department_id">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger field-error" id="err-department_id"></span>
                </div>

            </div>

            {{-- Answer Type --}}
            <div class="top-2 multi-radiios">
                <label>Select Answer Type <span class="text-danger">*</span></label>
                <div class="d-flex flex-column flex-md-row gap-3 gap-md-4 answer-type-group">
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="text"   id="type_text"   checked> <span>Text</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="single" id="type_single"> <span>Single Select</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="multi"  id="type_multi"> <span>Multi Select</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="image"  id="type_image"> <span>Image</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="video"  id="type_video"> <span>Video</span>
                    </label>
                </div>
                <span class="text-danger field-error" id="err-answer_type"></span>
            </div>

            {{-- ===== MEDIA UPLOAD SECTION (Image / Video) ===== --}}
            <div id="media-section" style="display:none;" class="top-2">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label id="media-label">Upload File <span class="text-danger">*</span></label>
                        <input type="file" name="media_file" id="media_file" class="form-control custom-input">
                        <small id="media-hint" class="text-muted"></small>
                        <span class="text-danger field-error" id="err-media_file"></span>
                    </div>

                    {{-- Preview --}}
                    <div class="col-12 col-md-6" id="media-preview-wrap" style="display:none;">
                        <label>Preview</label>
                        <div id="media-preview"></div>
                    </div>
                </div>

                {{-- Sub Answer Type -- shown only for image/video --}}
                <div class="top-2">
                    <label>Select Answer Type for this Question <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column flex-md-row gap-3 gap-md-4 answer-type-group">
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="text"   id="sub_type_text">   <span>Text</span>
                        </label>
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="single" id="sub_type_single"> <span>Single Select</span>
                        </label>
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="multi"  id="sub_type_multi">  <span>Multi Select</span>
                        </label>
                    </div>
                    <span class="text-danger field-error" id="err-sub_answer_type"></span>
                </div>
            </div>

            {{-- ===== TEXT ANSWER ===== --}}
            <div id="text-answer-section" class="top-2">
                <label>Enter Answer <span class="text-danger">*</span></label>
                <input type="text" name="text_answer" placeholder="Enter answer here"
                    class="custom-input form-control" id="text_answer">
                <span class="text-danger field-error" id="err-text_answer"></span>
            </div>

            {{-- ===== SINGLE SELECT ===== --}}
            <div id="single-answer-section" class="row g-3 top-2" style="display:none;">
                <span class="text-danger field-error col-12" id="err-single_options"></span>
                <span class="text-danger field-error col-12" id="err-correct_option"></span>
                @for ($i = 0; $i < 4; $i++)
                    <div class="col-12 col-md-6">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here"
                                class="custom-input form-control" name="single_options[]">
                            <input type="radio" class="correct-ones" name="correct_option"
                                value="{{ $i }}" style="width:20px;height:20px;">
                        </div>
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-muted">Select one radio button to mark the correct answer</small>
                </div>
            </div>

            {{-- ===== MULTI SELECT ===== --}}
            <div id="multi-answer-section" class="row g-3 top-2" style="display:none;">
                <span class="text-danger field-error col-12" id="err-multi_options"></span>
                <span class="text-danger field-error col-12" id="err-correct_options"></span>
                @for ($i = 0; $i < 4; $i++)
                    <div class="col-12 col-md-6">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here"
                                class="custom-input form-control" name="multi_options[]">
                            <input type="checkbox" class="correct-ones" name="correct_options[]"
                                value="{{ $i }}" style="width:20px;height:20px;">
                        </div>
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-muted">Select at least two checkboxes to mark correct answers</small>
                </div>
            </div>

            {{-- Status --}}
            <div class="row">
                <div class="col-12 col-md-12 top-2">
                    <label>Select Status <span class="text-danger">*</span></label>
                    <select name="status" class="custom-input form-control" id="status">
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <span class="text-danger field-error" id="err-status"></span>
                </div>
            </div>

            <button type="submit" class="btn btn-submit-form mt-3" id="submitBtn">
                Add Question
            </button>

        </form>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    // ==============================
    // ANSWER TYPE TOGGLE
    // ==============================
    function getActiveAnswerType() {
        return $('input[name="answer_type"]:checked').val();
    }
    function getActiveSubAnswerType() {
        return $('input[name="sub_answer_type"]:checked').val();
    }

    // Main answer type changed
    $('input[name="answer_type"]').on('change', function () {
        toggleMainSections($(this).val());
    });

    // Sub answer type changed (for image/video)
    $('input[name="sub_answer_type"]').on('change', function () {
        toggleAnswerFields($(this).val());
    });

    function toggleMainSections(type) {
        clearAllErrors();

        // Hide all answer fields first
        $('#text-answer-section').hide();
        $('#single-answer-section').hide();
        $('#multi-answer-section').hide();
        $('#media-section').hide();

        if (type === 'text') {
            $('#text-answer-section').show();
        } else if (type === 'single') {
            $('#single-answer-section').show();
        } else if (type === 'multi') {
            $('#multi-answer-section').show();
        } else if (type === 'image') {
            $('#media-section').show();
            $('#media-label').text('Upload Image *');
            $('#media-hint').text('Allowed: jpeg, png, jpg, gif, webp. Max: 5MB');
            $('#media_file').attr('accept', 'image/*');
            // Reset sub answer type and hide answer fields until sub type selected
            $('input[name="sub_answer_type"]').prop('checked', false);
        } else if (type === 'video') {
            $('#media-section').show();
            $('#media-label').text('Upload Video *');
            $('#media-hint').text('Allowed: mp4, mov, avi, wmv, webm. Max: 50MB');
            $('#media_file').attr('accept', 'video/*');
            $('input[name="sub_answer_type"]').prop('checked', false);
        }
    }

    function toggleAnswerFields(subType) {
        $('#text-answer-section').hide();
        $('#single-answer-section').hide();
        $('#multi-answer-section').hide();

        if (subType === 'text')   $('#text-answer-section').show();
        if (subType === 'single') $('#single-answer-section').show();
        if (subType === 'multi')  $('#multi-answer-section').show();
    }

    // ==============================
    // MEDIA PREVIEW
    // ==============================
    $('#media_file').on('change', function () {
        const file = this.files[0];
        if (!file) return;

        const type = getActiveAnswerType();
        $('#media-preview').empty();
        $('#media-preview-wrap').show();

        if (type === 'image') {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#media-preview').html('<img src="' + e.target.result + '" style="max-width:200px;max-height:150px;border-radius:6px;">');
            };
            reader.readAsDataURL(file);
        } else if (type === 'video') {
            const url = URL.createObjectURL(file);
            $('#media-preview').html('<video src="' + url + '" controls style="max-width:250px;max-height:150px;border-radius:6px;"></video>');
        }
    });

    // ==============================
    // CLEAR ERRORS
    // ==============================
    function clearAllErrors() {
        $('.field-error').text('');
        $('.custom-input').removeClass('is-invalid');
        $('#ajax-success, #ajax-error').hide().text('');
    }

    function showErrors(errors) {
        $.each(errors, function (field, messages) {
            const msg = Array.isArray(messages) ? messages[0] : messages;
            // Normalize field name for ID (e.g. department_id → err-department_id)
            const errId = '#err-' + field.replace(/\./g, '_');
            $(errId).text(msg);
            // Highlight input if exists
            $('[name="' + field + '"]').addClass('is-invalid');
        });
    }

    // ==============================
    // AJAX FORM SUBMIT
    // ==============================
    $('#questionForm').on('submit', function (e) {
        e.preventDefault();
        clearAllErrors();

        // Client-side validation
        if (!clientValidate()) return;

        const formData = new FormData(this);
        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: '{{ route("admin.qualification.store-question") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
            success: function (res) {
                if (res.success) {
                    $('#ajax-success').text(res.message).show();
                    setTimeout(function () {
                        window.location.href = res.redirect;
                    }, 1000);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Add Question');
                if (xhr.status === 422 && xhr.responseJSON) {
                    showErrors(xhr.responseJSON.errors || {});
                    const general = xhr.responseJSON.errors?.general;
                    if (general) $('#ajax-error').text(general).show();
                } else {
                    $('#ajax-error').text('Something went wrong. Please try again.').show();
                }
            }
        });
    });

    // ==============================
    // CLIENT-SIDE VALIDATION
    // ==============================
    function clientValidate() {
        let valid = true;

        if (!$.trim($('#question').val())) {
            $('#err-question').text('Please enter the question.');
            valid = false;
        }
        if (!$('#department_id').val()) {
            $('#err-department_id').text('Please select a department.');
            valid = false;
        }
        if (!$('#status').val()) {
            $('#err-status').text('Please select a status.');
            valid = false;
        }

        const type = getActiveAnswerType();

        if (type === 'image' || type === 'video') {
            if (!$('#media_file').val()) {
                $('#err-media_file').text('Please upload a ' + type + '.');
                valid = false;
            }
            if (!getActiveSubAnswerType()) {
                $('#err-sub_answer_type').text('Please select an answer type for this ' + type + ' question.');
                valid = false;
            }
        }

        const effectiveType = (type === 'image' || type === 'video') ? getActiveSubAnswerType() : type;

        if (effectiveType === 'text') {
            if (!$.trim($('#text_answer').val())) {
                $('#err-text_answer').text('Please enter the answer.');
                valid = false;
            }
        }

        if (effectiveType === 'single') {
            let filledOpts = 0;
            $('input[name="single_options[]"]').each(function () {
                if ($.trim($(this).val())) filledOpts++;
            });
            if (filledOpts < 4) {
                $('#err-single_options').text('All 4 answer options are required.');
                valid = false;
            }
            if (!$('input[name="correct_option"]:checked').length) {
                $('#err-correct_option').text('Please select exactly one correct answer.');
                valid = false;
            }
        }

        if (effectiveType === 'multi') {
            let filledOpts = 0;
            $('input[name="multi_options[]"]').each(function () {
                if ($.trim($(this).val())) filledOpts++;
            });
            if (filledOpts < 4) {
                $('#err-multi_options').text('All 4 answer options are required.');
                valid = false;
            }
            if ($('input[name="correct_options[]"]:checked').length < 2) {
                $('#err-correct_options').text('Please select at least two correct answers.');
                valid = false;
            }
        }

        return valid;
    }

    // Init: show text section by default
    toggleMainSections('text');
});
</script>
@endsection