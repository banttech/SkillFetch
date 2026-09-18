@extends('layout.admin.app')

@section('content')

<style>
    a.btn.btn-submit-form.bg-red.mt-3 {
    background: #d82525;
}.top-2 {
    margin-bottom: 1rem;
    gap: 5px 0;
}
.multi-radiios label.answer-type-radio{
    margin-bottom: 0;
}.text-danger {
    width: 100%;
}.text-purple {
    color: #000aff !important;
}
.text-orange{

color:#ff9800 !important;
}
#media-preview img{
    width: 120px;
    height: 120px;
    border-radius: 10px;
    border: 2px solid #ddd;margin: 18px 0 10px;
}

#media-preview video{
        max-width: 150px;
    max-height: 150px;
    border-radius: 6px;
    border: 2px solid #ddd;
    margin: 15px 0 0;
}

</style>
    <div class="content-card add-quests">

        <div class="div-main-haeds">
            <h2 class="page-title">Edit Question</h2>
        </div>

        <div id="ajax-success" class="alert alert-success" style="display:none;"></div>
        <div id="ajax-error"   class="alert alert-danger"  style="display:none;"></div>

       <form id="questionForm" enctype="multipart/form-data" class="add-ques">
         
            @csrf

            <div class="row g-3">

                {{-- Question --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Enter the Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" placeholder="Enter your question here"
                        class="custom-input form-control" id="question"
                        value="{{ $question->question }}">
                    <span class="text-danger field-error" id="err-question"></span>
                </div>

                {{-- Department --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Select Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="custom-input form-control" id="department_id">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}" {{ $question->department_id == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-danger field-error" id="err-department_id"></span>
                </div>

            </div>

            {{-- Answer Type --}}
              <div class="multi-radiios">
                <label>Select Answer Type <span class="text-danger">*</span></label>
                <div class="d-flex flex-column flex-md-row gap-3 gap-md-4 answer-type-group mb-2">
                   <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="text"
                            {{ $question->answer_type === 'text' ? 'checked' : '' }}> <span>Text</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="single"
                            {{ $question->answer_type === 'single' ? 'checked' : '' }}> <span>Single Select</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="multi"
                            {{ $question->answer_type === 'multi' ? 'checked' : '' }}> <span>Multi Select</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="image"
                            {{ $question->answer_type === 'image' ? 'checked' : '' }}> <span>Image</span>
                    </label>
                    <label class="answer-type-radio">
                        <input type="radio" name="answer_type" value="video"
                            {{ $question->answer_type === 'video' ? 'checked' : '' }}> <span>Video</span>
                    </label>
                </div>
                <span class="text-danger field-error" id="err-answer_type"></span>
            </div>

            {{-- ===== MEDIA SECTION ===== --}}
            <div id="media-section" style="display:none;" class="top-2">
                <div class="row g-3">
                   <div class="col-12 col-md-12 mt-3">
                        <label id="media-label">Upload File <span class="text-danger">*</span></label>
                        <input type="file" name="media_file" id="media_file" class="form-control custom-input">
                        <small id="media-hint" class="text-orange"></small> <br>
                        <span class="text-danger field-error" id="err-media_file"></span>
                    </div>

                    {{-- Existing media preview --}}
                    <div class="col-12 col-md-12" id="media-preview-wrap">
                        {{-- <label>Current File</label> --}}
                       
                        <div id="media-preview">
                            @if($question->media_path && $question->answer_type === 'image')
                                <img src="{{ asset('storage/' . $question->media_path) }}"
                                    style="max-width:200px;max-height:150px;border-radius:6px;" id="existing-media-img">
                            @elseif($question->media_path && $question->answer_type === 'video')
                                <video src="{{ asset('storage/' . $question->media_path) }}"
                                    controls style="max-width:250px;max-height:150px;border-radius:6px;" id="existing-media-vid"></video>
                            @endif
                        </div>
                         <small class="text-purple" id="keep-media-note">
                        
                            @if($question->media_path) Leave blank to keep existing file. @endif
                        </small>
                    </div>
                </div>

                {{-- Sub Answer Type --}}
                <div class="mt-3">
                    <label>Select Answer Type for this Question <span class="text-danger">*</span></label>
                    <div class="d-flex flex-column flex-md-row gap-3 gap-md-4 answer-type-group">
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="text"
                                {{ $question->sub_answer_type === 'text' ? 'checked' : '' }}> <span>Text</span>
                        </label>
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="single"
                                {{ $question->sub_answer_type === 'single' ? 'checked' : '' }}> <span>Single Select</span>
                        </label>
                        <label class="answer-type-radio">
                            <input type="radio" name="sub_answer_type" value="multi"
                                {{ $question->sub_answer_type === 'multi' ? 'checked' : '' }}> <span>Multi Select</span>
                        </label>
                    </div>
                    <span class="text-danger field-error" id="err-sub_answer_type"></span>
                </div>
            </div>

            {{-- ===== TEXT ANSWER ===== --}}
           <div id="text-answer-section" class="top-2 mt-3" style="display:none;">
             
                <label>Enter Answer <span class="text-danger">*</span></label>
                <input type="text" name="text_answer" placeholder="Enter answer here"
                    class="custom-input form-control" id="text_answer"
                    value="{{ $question->answers->where('is_correct', 1)->first()?->answer ?? '' }}">
                <span class="text-danger field-error" id="err-text_answer"></span>
            </div>

            {{-- ===== SINGLE SELECT ===== --}}
            @php
                $singleAnswers    = $question->answers->values();
                $correctSingleIdx = null;
                foreach ($singleAnswers as $idx => $ans) {
                    if ($ans->is_correct) { $correctSingleIdx = $idx; break; }
                }
            @endphp
            <div id="single-answer-section" class="row g-3 top-2" style="display:none;">
                <span class="text-danger field-error col-12" id="err-single_options"></span>
                <span class="text-danger field-error col-12" id="err-correct_option"></span>
                @for ($i = 0; $i < 4; $i++)
                    <div class="col-12 col-md-6 mt-3">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here" class="custom-input form-control"
                                name="single_options[]"
                                value="{{ $singleAnswers[$i]->answer ?? '' }}">
                            <input type="radio" class="correct-ones" name="correct_option" value="{{ $i }}"
                                {{ $correctSingleIdx === $i ? 'checked' : '' }}
                                style="width:20px;height:20px;">
                        </div>
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-orange">Select one radio button to mark the correct answer</small>
                </div>
            </div>

            {{-- ===== MULTI SELECT ===== --}}
            @php
                $multiAnswers    = $question->answers->values();
                $correctMultiIdx = $multiAnswers->where('is_correct', 1)->keys()->toArray();
            @endphp
            <div id="multi-answer-section" class="row g-3 top-2" style="display:none;">
                <span class="text-danger field-error col-12" id="err-multi_options"></span>
                <span class="text-danger field-error col-12" id="err-correct_options"></span>
                @for ($i = 0; $i < 4; $i++)
                    <div class="col-12 col-md-6 mt-3">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here" class="custom-input form-control"
                                name="multi_options[]"
                                value="{{ $multiAnswers[$i]->answer ?? '' }}">
                            <input type="checkbox" class="correct-ones" name="correct_options[]" value="{{ $i }}"
                                {{ in_array($i, $correctMultiIdx) ? 'checked' : '' }}
                                style="width:20px;height:20px;">
                        </div>
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-orange">Select at least two checkboxes to mark correct answers</small>
                </div>
            </div>

            {{-- Status --}}
            <div class="row">
                <div class="col-12 col-md-12 top-2">
                    <label>Select Status <span class="text-danger">*</span></label>
                    <select name="status" class="custom-input form-control" id="status">
                        <option value="">Select Status</option>
                        <option value="1" {{ $question->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $question->status == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <span class="text-danger field-error" id="err-status"></span>
                </div>
            </div>

            <button type="submit" class="btn btn-submit-form mt-3" id="submitBtn">Update Question</button>
             <a href="{{ route('admin.qualification.setuptest') }}" class="btn btn-submit-form bg-red mt-3 ">Cancel</a>

        </form>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    const INITIAL_TYPE     = '{{ $question->answer_type }}';
    const INITIAL_SUB_TYPE = '{{ $question->sub_answer_type ?? "" }}';

    // ==============================
    // ANSWER TYPE TOGGLE
    // ==============================
    function getActiveAnswerType() {
        return $('input[name="answer_type"]:checked').val();
    }
    function getActiveSubAnswerType() {
        return $('input[name="sub_answer_type"]:checked').val();
    }

    $('input[name="answer_type"]').on('change', function () {
        toggleMainSections($(this).val());
    });

    $('input[name="sub_answer_type"]').on('change', function () {
        toggleAnswerFields($(this).val());
    });

    function toggleMainSections(type) {
        clearAllErrors();
        $('#text-answer-section, #single-answer-section, #multi-answer-section, #media-section').hide();

        if (type === 'text')   $('#text-answer-section').show();
        if (type === 'single') $('#single-answer-section').show();
        if (type === 'multi')  $('#multi-answer-section').show();

        if (type === 'image') {
            $('#media-section').show();
            $('#media-label').text('Upload Image *');
            $('#media-hint').text('Allowed: jpeg, png, jpg, gif, webp. Max: 5MB');
            $('#media_file').attr('accept', 'image/*');
            // Show answer fields for current sub type
            if (getActiveSubAnswerType()) toggleAnswerFields(getActiveSubAnswerType());
        }
        if (type === 'video') {
            $('#media-section').show();
            $('#media-label').text('Upload Video *');
            $('#media-hint').text('Allowed: mp4, mov, webm. Max: 50MB');
            $('#media_file').attr('accept', 'video/*');
            if (getActiveSubAnswerType()) toggleAnswerFields(getActiveSubAnswerType());
        }
    }

    function toggleAnswerFields(subType) {
        $('#text-answer-section, #single-answer-section, #multi-answer-section').hide();
        if (subType === 'text')   $('#text-answer-section').show();
        if (subType === 'single') $('#single-answer-section').show();
        if (subType === 'multi')  $('#multi-answer-section').show();
    }

    // ==============================
    // NEW FILE PREVIEW
    // ==============================
    $('#media_file').on('change', function () {
        const file = this.files[0];
        if (!file) return;
        const type = getActiveAnswerType();
        $('#media-preview').empty();
        if (type === 'image') {
            const reader = new FileReader();
            reader.onload = function (e) {
                $('#media-preview').html('<img src="' + e.target.result + '" style="max-width:200px;max-height:150px;border-radius:6px;">');
            };
            reader.readAsDataURL(file);
        } else {
            const url = URL.createObjectURL(file);
            $('#media-preview').html('<video src="' + url + '" controls style="max-width:250px;max-height:150px;border-radius:6px;"></video>');
        }
        $('#keep-media-note').text('New file will replace the existing one.');
    });

    // ==============================
    // CLEAR ERRORS
    // ==============================
    function clearAllErrors() {
        $('.field-error').text('');
        $('.custom-input');
        $('#ajax-success, #ajax-error').hide().text('');
    }

    function showErrors(errors) {
        $.each(errors, function (field, messages) {
            const msg    = Array.isArray(messages) ? messages[0] : messages;
            const errId  = '#err-' + field.replace(/\./g, '_');
            $(errId).text(msg);
            $('[name="' + field + '"]');
        });
    }

    // ==============================
    // AJAX FORM SUBMIT
    // ==============================
    $('#questionForm').on('submit', function (e) {
        e.preventDefault();
        clearAllErrors();

        if (!clientValidate()) return;

        const formData = new FormData(this);
        // Laravel PUT method spoofing via form data
        formData.append('_method', 'POST');

        const $btn = $('#submitBtn');
        $btn.prop('disabled', true).text('Updating...');

        $.ajax({
            url: '{{ route("admin.qualification.update-question", $question->id) }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('input[name="_token"]').val() },
            success: function (res) {
                if (res.success) {
                    //$('#ajax-success').text(res.message).show();
                    // setTimeout(function () {
                        window.location.href = res.redirect;
                    // }, 1000);
                }
            },
            error: function (xhr) {
                $btn.prop('disabled', false).text('Update Question');
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
            // Only require file if no existing media
            const hasExisting = $('#existing-media-img').length > 0 || $('#existing-media-vid').length > 0;
            if (!$('#media_file').val() && !hasExisting) {
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

    // ==============================
    // INITIALISE ON PAGE LOAD
    // ==============================
    toggleMainSections(INITIAL_TYPE);
    if ((INITIAL_TYPE === 'image' || INITIAL_TYPE === 'video') && INITIAL_SUB_TYPE) {
        toggleAnswerFields(INITIAL_SUB_TYPE);
    }
});
</script>
@endsection