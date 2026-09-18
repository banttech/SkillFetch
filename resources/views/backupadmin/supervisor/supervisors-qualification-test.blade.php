@extends('layout.admin.app')

@section('content')
    <style>
        .btn-verify:disabled {
            cursor: not-allowed !important;
            opacity: 0.6 !important;

        }

        .btn-reject:disabled {
            cursor: not-allowed !important;
            opacity: 0.6 !important;

        }

        .correction-checkbox:disabled {
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }
        .textDisabled{
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }
        .tag-btn{
            cursor: default !important;
        }
    </style>
    <div class="content-card">

        <div class="div-main-haeds">
            <h2 class="page-title">Qualification Test Review</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Supervisor Basic Details --}}
        <div class="job-details-box ad-new-details">
            <div class="detail-row">
                <div class="detail-item">
                    <label>Sup ID</label>
                    <p>{{ $id }}</p>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <p>{{ $email }}</p>
                </div>
                <div class="detail-item">
                    <label>Test Submission Time</label>
                    <p>{{ $test_date }}</p>
                </div>
            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <label>Name</label>
                    <p>{{ $name }}</p>
                </div>
                <div class="detail-item">
                    <label>Mobile No.</label>
                    <p>{{ $mobile }}</p>
                </div>
                <div class="detail-item">
                    <label>Submission Type</label>
                    <p>{{ ucfirst($attempt->test_submission_status) }}</p>
                </div>
            </div>

             @if ($attempt->status == 'underReview')

             @elseif($attempt->status == 'pass')
             <div class="text-success" style="text-align:center;width:100%; margin-top:10px;">
                <strong>The supervisor has passed this test attempt</strong>    
            </div>
            @elseif($attempt->status == 'fail')
            <div class="text-danger" style="text-align:center;width:100%; margin-top:10px;">
                <strong>The supervisor has failed this test attempt</strong>
            </div>
            @endif
        </div>

        {{-- Questions Section --}}
        <div class="div-inner-sections">

            @php $questionNumber = 1; @endphp

            @foreach ($groupedQuestions as $departmentName => $questions)
                <h3 class="department-heading" style="margin: 20px 0; color: #0066cc;">
                    Department: {{ $departmentName }}
                </h3>

                @foreach ($questions as $attemptedQuestion)
                    <div class="question-item" data-question-id="{{ $attemptedQuestion->id }}">
                        <div class="question-header">

                            <div class="question-text">
                                <div class="question-number">{{ str_pad($questionNumber++, 2, '0', STR_PAD_LEFT) }}</div>

                                <div class="div-ques">
                                    <div class="question-title">
                                        {{ $attemptedQuestion->question }}
                                    </div>

                                    {{-- TEXT TYPE --}}
                                    @if ($attemptedQuestion->answer_type === 'text')
                                        <div class="question-content" style="margin-top: 10px;">
                                            <strong>Correct Answer:</strong>
                                            {{ $attemptedQuestion->attemptedAnswers->first()->answer }}
                                        </div>
                                        <div class="question-content" style="margin-top: 5px;">
                                            <strong>User's Answer:</strong>
                                            @if ($attemptedQuestion->is_attempted)
                                                <span
                                                    style="color: {{ $attemptedQuestion->is_corrected_by_user ? 'green' : 'red' }}">
                                                    {{ $attemptedQuestion->attemptedAnswers->first()->user_answer ?? 'No answer provided' }}
                                                </span>
                                            @else
                                                <span style="color: red">Not Attempted</span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- SINGLE SELECT (RADIO) --}}
                                    @if ($attemptedQuestion->answer_type === 'single')
                                        <div class="radio-options" style="margin-top: 10px;">
                                            @foreach ($attemptedQuestion->attemptedAnswers as $option)
                                                <div class="radio-option" style="margin: 5px 0;">
                                                    <input type="radio" disabled
                                                        {{ $option->user_answer == 1 ? 'checked' : '' }}
                                                        style="margin-right: 8px;">
                                                    <span style="color: {{ $option->is_correct ? 'green' : 'inherit' }}">
                                                        {{ $option->answer }}
                                                        @if ($option->is_correct)
                                                            <strong>(Correct Answer)</strong>
                                                        @endif
                                                        @if ($option->user_answer == 1)
                                                            <em>(User Selected)</em>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if (!$attemptedQuestion->is_attempted)
                                               <strong>User's Answer:</strong> <span style="color: red">Not Attempted</span>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- MULTI SELECT (CHECKBOX) --}}
                                    @if ($attemptedQuestion->answer_type === 'multi')
                                        <div class="checkbox-options" style="margin-top: 10px;">
                                            @foreach ($attemptedQuestion->attemptedAnswers as $option)
                                                <div class="checkbox-option" style="margin: 5px 0;">
                                                    <input type="checkbox" disabled
                                                        {{ $option->user_answer == 1 ? 'checked' : '' }}
                                                        style="margin-right: 8px;">
                                                    <span style="color: {{ $option->is_correct ? 'green' : 'inherit' }}">
                                                        {{ $option->answer }}
                                                        @if ($option->is_correct)
                                                            <strong>(Correct Answer)</strong>
                                                        @endif
                                                        @if ($option->user_answer == 1)
                                                            <em>(User Selected)</em>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                            @if (!$attemptedQuestion->is_attempted)
                                               <strong>User's Answer:</strong> <span style="color: red">Not Attempted</span>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>

                            @php
                                $isDisabled =
                                    in_array($attempt->status, ['pass', 'fail']) || !$attemptedQuestion->is_attempted;
                            @endphp

                            <div class="correct-badge  {{ $isDisabled ? 'textDisabled' : '' }}">
                                <label class="checkbox-custom">
                                    {{-- <input type="checkbox" class="correction-checkbox"
                                        data-question-id="{{ $attemptedQuestion->id }}"
                                        {{ $attemptedQuestion->is_corrected_by_user ? 'checked' : '' }}
                                        {{ in_array($attempt->status, ['pass', 'fail']) ? 'disabled' : '' }}> --}}
                                    <input type="checkbox" class="correction-checkbox"
                                        data-question-id="{{ $attemptedQuestion->id }}"
                                        {{ $attemptedQuestion->is_corrected_by_user ? 'checked' : '' }}
                                        {{ $isDisabled ? 'disabled' : '' }}>
                                    <span class="checkmark"></span>
                                </label>
                                <span class="correct-text">Correct</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach

            {{-- Score Section --}}
            <div class="score-section">
                <div class="d-flex align-items-center">
                    <span class="score-label">Supervisor Score:</span>
                    <input type="text" class="score-input" id="currentScore" value="{{ $currentScore }}" readonly>
                    <span class="score-total">/ {{ $attempt->total_marks }}</span>
                    <span id="passingStatus"
                        style="margin-left: 20px; font-weight: bold; color: {{ $currentScore >= $attempt->passing_marks ? 'green' : 'red' }}">
                        (Passing Marks: {{ $attempt->passing_marks }})
                    </span>
                </div>
            </div>

            @if (count($skills) > 0)
                <div class="tags-section">
                    <div class="tags-label">Skill Tags:</div>
                    <div class="tags-container">

                        @foreach ($skills as $skill)
                            <button class="tag-btn">{{ $skill }}</button>
                        @endforeach

                    </div>
                </div>
            @endif

            {{-- Action Buttons --}}
            @if ($attempt->status == 'underReview')
                <form id="reviewForm" method="POST">
                    @csrf
                    <div class="action-buttons">
                        <button type="submit" class="btn-verify" id="verifyBtn"
                            formaction="{{ route('admin.test-review.verify', $attempt->id) }}"
                            {{ $currentScore < $attempt->passing_marks ? 'disabled' : '' }}
                            onclick="return confirm('Are you sure you want to verify this test? This action will mark the supervisor as pass.');">
                            <img src="{{ asset('admin_assets/images/double-tick.png') }}" alt="verify">
                            Verify (Pass)
                        </button>

                        <button type="submit" class="btn-reject" id="rejectBtn"
                            formaction="{{ route('admin.test-review.reject', $attempt->id) }}"
                            {{ $currentScore >= $attempt->passing_marks ? 'disabled' : '' }}
                            onclick="return confirm('Are you sure you want to reject this test? This action will mark the supervisor as failed.');">
                            <img src="{{ asset('admin_assets/images/Close.png') }}" alt="reject">
                            Reject (Fail)
                        </button>
                    </div>
                </form>
            @elseif($attempt->status == 'fail')
                {{-- <p class="text-danger"><b>You have marked status failed of the test.</b></p> --}}
            @elseif($attempt->status == 'pass')
                {{-- <p class="text-success"><b>You have marked status passed of the test.</b></p> --}}
            @endif
        </div>

    </div>

    <script>
        const PASSING_MARKS = {{ $attempt->passing_marks }};
        const PER_QUESTION_MARKS = {{ $attempt->per_question_marks }};
        const TOTAL_MARKS = {{ $attempt->total_marks }};

        document.addEventListener('DOMContentLoaded', function() {
            // Setup correction checkbox listeners
            document.querySelectorAll('.correction-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleCorrection(this);
                });
            });
        });

        function toggleCorrection(checkbox) {
            const questionId = checkbox.dataset.questionId;
            const isCorrect = checkbox.checked ? 1 : 0;

            fetch("{{ route('admin.test-review.toggle-correction') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        question_id: questionId,
                        is_correct: isCorrect
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update score display
                        document.getElementById('currentScore').value = data.new_score;

                        // Update passing status
                        const statusEl = document.getElementById('passingStatus');
                        statusEl.style.color = data.can_pass ? 'green' : 'red';
                        statusEl.textContent = `(Passing Marks: ${PASSING_MARKS})`;

                        // Enable/disable verify button
                        const verifyBtn = document.getElementById('verifyBtn');
                        const rejectBtn = document.getElementById('rejectBtn');
                        verifyBtn.disabled = !data.can_pass;
                        rejectBtn.disabled = data.can_pass;

                    } else {
                        alert('Failed to update: ' + data.message);
                        // Revert checkbox
                        checkbox.checked = !checkbox.checked;
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('An error occurred');
                    // Revert checkbox
                    checkbox.checked = !checkbox.checked;
                });
        }
    </script>
@endsection
