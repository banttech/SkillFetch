@extends('layout.admin.app')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="content-card">

        <div class="div-main-haeds">
            <h2 class="page-title">Edit Question</h2>
        </div>

        {{-- MAIN FORM --}}
        <form class="add-ques" action="{{ route('admin.qualification.update-question', $question->id) }}" method="POST">
            @csrf

            <div class="row g-3">

                {{-- Question --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Enter the Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" placeholder="Enter your question here"
                        class="custom-input form-control" value="{{ old('question', $question->question) }}">
                    @error('question')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Department --}}
                <div class="col-12 col-md-6 top-2">
                    <label>Select Department <span class="text-danger">*</span></label>
                    <select name="department_id" class="custom-input form-control">
                        <option value="">Select Department</option>
                        @foreach ($departments as $dept)
                            <option value="{{ $dept->id }}"
                                {{ old('department_id', $question->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            @php
                $currentAnswerType = old('answer_type', $question->answer_type);
            @endphp

            <div class="top-2 multi-radiios">
                <label>Select Answer Type <span class="text-danger">*</span></label>
                <div class="d-flex flex-column flex-md-row gap-3 gap-md-4 answer-type-group">
                    <div class="form-check">
                        <input type="radio" name="answer_type" value="text" id="type_text"
                            onclick="toggleAnswerFields('text')" {{ $currentAnswerType == 'text' ? 'checked' : '' }}>
                        <label class="form-check-label radio-text" for="type_text">Text</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="answer_type" value="single" id="type_single"
                            onclick="toggleAnswerFields('single')" {{ $currentAnswerType == 'single' ? 'checked' : '' }}>
                        <label class="form-check-label radio-text" for="type_single">Single Select</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="answer_type" value="multi" id="type_multi"
                            onclick="toggleAnswerFields('multi')" {{ $currentAnswerType == 'multi' ? 'checked' : '' }}>
                        <label class="form-check-label radio-text" for="type_multi">Multi Select</label>
                    </div>
                </div>
                @error('answer_type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- TEXT TYPE --}}
            <div id="text-answer-section" class="top-2">
                <label>Enter Answer <span class="text-danger">*</span></label>
                <input type="text" name="text_answer" placeholder="Enter answer here" class="custom-input form-control"
                    value="{{ old('text_answer', $question->answer_type == 'text' && $question->answers->first() ? $question->answers->first()->answer : '') }}">
                @error('text_answer')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- SINGLE SELECT TYPE --}}
            <div id="single-answer-section" class="row g-3 top-2" style="display:none;">
                @error('correct_options')
                    <div class="col-12">
                        <span class="text-danger">{{ $message }}</span>
                    </div>
                @enderror
                @for ($i = 0; $i < 4; $i++)
                    @php
                        $existingAnswer =
                            $question->answer_type == 'single' && isset($question->answers[$i])
                                ? $question->answers[$i]
                                : null;
                        $oldValue = old('single_options.' . $i, $existingAnswer ? $existingAnswer->answer : '');
                        $isCorrect =
                            old('correct_option') !== null
                                ? old('correct_option') == $i
                                : $existingAnswer && $existingAnswer->is_correct;
                    @endphp
                    <div class="col-12 col-md-6">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here" class="custom-input form-control"
                                name="single_options[]" value="{{ $oldValue }}">
                            <input type="radio" name="correct_option" value="{{ $i }}"
                                {{ $isCorrect ? 'checked' : '' }} style="width: 20px; height: 20px;">
                        </div>
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-muted">Select one radio button to mark the correct answer</small>
                </div>
            </div>

            {{-- MULTI SELECT TYPE --}}
            <div id="multi-answer-section" class="row g-3 top-2" style="display:none;">
                @error('correct_options')
                    <div class="col-12">
                        <span class="text-danger">{{ $message }}</span>
                    </div>
                @enderror
                @for ($i = 0; $i < 4; $i++)
                    @php
                        $existingAnswer =
                            $question->answer_type == 'multi' && isset($question->answers[$i])
                                ? $question->answers[$i]
                                : null;
                        $oldValue = old('multi_options.' . $i, $existingAnswer ? $existingAnswer->answer : '');
                        $isCorrect =
                            old('correct_options') !== null
                                ? in_array($i, old('correct_options', []))
                                : $existingAnswer && $existingAnswer->is_correct;
                    @endphp
                    <div class="col-12 col-md-6">
                        <label>Enter Option {{ $i + 1 }} <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="text" placeholder="Enter option here" class="custom-input form-control"
                                name="multi_options[]" value="{{ $oldValue }}">
                            <input type="checkbox" name="correct_options[]" value="{{ $i }}"
                                {{ $isCorrect ? 'checked' : '' }} style="width: 20px; height: 20px;">
                        </div>
                        @error('multi_options.' . $i)
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endfor
                <div class="col-12">
                    <small class="text-muted">Select at least two checkboxes to mark correct answers</small>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12 top-2">
                    <label>Select Status <span class="text-danger">*</span></label>
                    <select name="status" class="custom-input form-control">
                        <option value="">Select Status</option>
                        <option value="1" {{ $question->status == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $question->status == 0 ? 'selected' : '' }}>Inactive</option>

                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>



            <button type="submit" class="btn btn-submit-form mt-3">
                Update Question
            </button>

        </form>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (old('answer_type'))
                toggleAnswerFields('{{ old('answer_type') }}');
            @else
                toggleAnswerFields('{{ $question->answer_type }}');
            @endif
        });

        function toggleAnswerFields(type) {
            document.getElementById('text-answer-section').style.display = (type === 'text') ? 'block' : 'none';
            document.getElementById('single-answer-section').style.display = (type === 'single') ? 'flex' : 'none';
            document.getElementById('multi-answer-section').style.display = (type === 'multi') ? 'flex' : 'none';
        }
    </script>
@endsection
