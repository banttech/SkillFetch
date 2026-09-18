@extends('layout.supervisor.app')

@section('content')
   
    <style>
      
        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            text-align: center;
            color: white;
        }

        .loading-spinner .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Pulse animation for timer */
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }

        .timer-critical {
            animation: pulse 1s infinite;
        }

        /* Success animation for answered questions */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .badge-animated {
            animation: fadeIn 0.3s ease-in;
        }
    </style>

    <div class="content">
        <div class="container-fluid">

            {{-- Loading Overlay --}}
            <div class="loading-overlay" id="loadingOverlay">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <h4>Processing...</h4>
                </div>
            </div>

            {{-- TEST HEADER --}}
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="test-name-box">
                        <p>
                            <span class="label">Test Name:</span>
                            <span class="ms-2">{{ $attempt->test_name }}</span>
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="questions-box">
                        <p class="div-top-ques2">No. of Questions: {{ $attempt->total_question }}</p><br>
                        <p class="div-top-ques3">Passing Marks: {{ $attempt->passing_marks }}</p>
                        <p class="marks-info">Each question carries {{ $attempt->per_question_marks }} mark(s)</p>
                    </div>
                </div>
            </div>

            {{-- ATTEMPT + TIMER --}}
            <div class="header-section">
                <div class="attempted-badge">
                    Attempted <span id="attemptedCount">{{ $attemptedCount }}</span>/{{ $attempt->total_question }}
                </div>
                <div class="timer-info">
                    <p class="timer" id="timerDisplay">
                        <i class="fas fa-clock"></i> Time Remaining: <span id="timeRemaining">Loading...</span>
                    </p>
                </div>
            </div>

            {{-- SHOW QUESTIONS BY DEPARTMENT --}}
            <form id="testForm">
                @csrf
                <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

                @foreach ($groupedQuestions as $departmentName => $questions)
                    <div class="question-card">
                        <h2 class="department-title">Department: {{ $departmentName }}</h2>

                        @foreach ($questions as $index => $attemptedQuestion)
                            <div class="question-item" data-question-id="{{ $attemptedQuestion->question_id }}">

                                {{-- QUESTION TEXT --}}
                                <p class="question-text">
                                    {{ $loop->parent->iteration }}.{{ $loop->iteration }}.
                                    {{ $attemptedQuestion->question }}
                                    @if ($attemptedQuestion->is_attempted)
                                        <span class="badge bg-success ms-2 badge-animated">Answered</span>
                                    @endif
                                </p>

                                {{-- TEXT TYPE QUESTION --}}
                                @if ($attemptedQuestion->answer_type === 'text')
                                    @php
                                        $userAnswer = $attemptedQuestion->attemptedAnswers->first()->user_answer ?? '';
                                    @endphp
                                    <input type="text" class="text-input answer-input" placeholder="Write your answer..."
                                        data-question-id="{{ $attemptedQuestion->question_id }}" data-answer-type="text"
                                        value="{{ $userAnswer }}">
                                @endif

                                {{-- MULTI-SELECT QUESTION --}}
                                @if ($attemptedQuestion->answer_type === 'multi')
                                    <div class="checkbox-group">
                                        @foreach ($attemptedQuestion->attemptedAnswers as $option)
                                            <div class="checkbox-option">
                                                <input type="checkbox" id="opt{{ $option->id }}" class="answer-input"
                                                    data-question-id="{{ $attemptedQuestion->question_id }}"
                                                    data-answer-type="multi" data-answer-id="{{ $option->answer_id }}"
                                                    value="{{ $option->answer_id }}"
                                                    {{ $option->user_answer == 1 ? 'checked' : '' }}>
                                                <label for="opt{{ $option->id }}">
                                                    {{ $option->answer }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- SINGLE-SELECT QUESTION --}}
                                @if ($attemptedQuestion->answer_type === 'single')
                                    <div class="radio-group">
                                        @foreach ($attemptedQuestion->attemptedAnswers as $option)
                                            <div class="radio-option">
                                                <input type="radio" id="opt{{ $option->id }}" class="answer-input"
                                                    data-question-id="{{ $attemptedQuestion->question_id }}"
                                                    data-answer-type="single"
                                                    name="question_{{ $attemptedQuestion->question_id }}"
                                                    value="{{ $option->answer_id }}"
                                                    {{ $option->user_answer == 1 ? 'checked' : '' }}>
                                                <label for="opt{{ $option->id }}">
                                                    {{ $option->answer }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        @endforeach

                    </div>
                @endforeach

                {{-- SUBMIT BUTTON --}}
                <div class="submit-section">
                    <button type="button" class="submit-btn" id="submitTestBtn">
                        <i class="fas fa-paper-plane"></i> Submit Test
                    </button>
                </div>

            </form>

        </div>
    </div>

   

    <script>
        const ATTEMPT_ID = {{ $attempt->id }};
        const CSRF_TOKEN = "{{ csrf_token() }}";
        const END_TIME = new Date("{{ $attempt->end_time->toIso8601String() }}").getTime();
        let timerInterval;
        let notified15Min = false;
        let notified2Min = false;
        let lastSyncTime = Date.now();


        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            showToast('Test started! Good luck! 🎯', 'info');
            startRealTimeTimer();
            setupAnswerListeners();
            setupSubmitButton();

            // Sync with server every 30 seconds
            setInterval(syncWithServer, 30000);

            // Re-sync on visibility change (tab switch)
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    syncWithServer();
                }
            });
        });

        // Real-time timer using server end_time
        function startRealTimeTimer() {
            updateTimerDisplay();

            timerInterval = setInterval(() => {
                updateTimerDisplay();

                const remainingSeconds = getRemainingSeconds();

                // 15 minute warning
                if (remainingSeconds === 900 && !notified15Min) {
                    notified15Min = true;
                    showToast('⏰ 15 minutes remaining!', 'warning');
                }

                // 2 minute warning
                if (remainingSeconds === 120 && !notified2Min) {
                    notified2Min = true;
                    showToast('⏰ Only 2 minutes left!', 'error');
                    document.getElementById('timerDisplay').classList.add('timer-critical');
                }

                // Auto-submit when time expires
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    autoSubmitTest();
                }
            }, 1000);
        }

        // Calculate remaining seconds from server end_time
        function getRemainingSeconds() {
            const now = Date.now();
            const remaining = Math.floor((END_TIME - now) / 1000);
            return Math.max(0, remaining);
        }

        // Update timer display
        function updateTimerDisplay() {
            const remainingSeconds = getRemainingSeconds();

            const minutes = Math.floor(remainingSeconds / 60);
            const seconds = remainingSeconds % 60;
            const display = `${minutes} min ${seconds.toString().padStart(2, '0')} sec`;
            document.getElementById('timeRemaining').textContent = display;

            // Change color when time is low
            const timerEl = document.getElementById('timerDisplay');
            if (remainingSeconds <= 120) {
                timerEl.style.color = 'red';
            } else if (remainingSeconds <= 900) {
                timerEl.style.color = 'orange';
            }
        }

        // Sync with server to prevent drift
        function syncWithServer() {
            fetch("{{ route('supervisor.test.remaining-time') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        attempt_id: ATTEMPT_ID
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.expired) {
                        clearInterval(timerInterval);
                        autoSubmitTest();
                    }
                    lastSyncTime = Date.now();
                })
                .catch(err => console.error('Sync error:', err));
        }

        // Toast notification helper
        function showToast(message, type = 'info') {
            switch (type) {
                case 'success':
                    toastr.success(message);
                    break;
                case 'error':
                    toastr.error(message);
                    break;
                case 'warning':
                    toastr.warning(message);
                    break;
                case 'info':
                default:
                    toastr.info(message);
                    break;
            }
        }

        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').classList.add('active');
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }

        // Setup answer input listeners
        function setupAnswerListeners() {
            // Text inputs - save on blur
            document.querySelectorAll('.answer-input[data-answer-type="text"]').forEach(input => {
                let typingTimer;

                input.addEventListener('input', function() {
                    clearTimeout(typingTimer);
                    typingTimer = setTimeout(() => {
                        if (this.value.trim()) {
                            saveAnswer(this.dataset.questionId, this.value, 'text');
                        }
                    }, 1000); // Save after 1 second of no typing
                });

                input.addEventListener('blur', function() {
                    if (this.value.trim()) {
                        saveAnswer(this.dataset.questionId, this.value, 'text');
                    }
                });
            });

            // Radio buttons - save on change
            document.querySelectorAll('.answer-input[data-answer-type="single"]').forEach(input => {
                input.addEventListener('change', function() {
                    saveAnswer(this.dataset.questionId, this.value, 'single');
                });
            });

            // Checkboxes - save on change
            document.querySelectorAll('.answer-input[data-answer-type="multi"]').forEach(input => {
                input.addEventListener('change', function() {
                    const questionId = this.dataset.questionId;
                    const checked = document.querySelectorAll(
                        `.answer-input[data-question-id="${questionId}"][data-answer-type="multi"]:checked`
                    );
                    const values = Array.from(checked).map(cb => cb.value);

                    if (values.length > 0) {
                        saveAnswer(questionId, values, 'multi');
                    }
                });
            });
        }

        // Save answer via AJAX
        function saveAnswer(questionId, answer, type) {
            fetch("{{ route('supervisor.test.save-answer') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        attempt_id: ATTEMPT_ID,
                        question_id: questionId,
                        answer: answer
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // Update attempted count
                        document.getElementById('attemptedCount').textContent = data.attempted_count;

                        // Mark question as answered with animation
                        const questionDiv = document.querySelector(`[data-question-id="${questionId}"]`);
                        const existingBadge = questionDiv.querySelector('.badge');

                        if (!existingBadge) {
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-success ms-2 badge-animated';
                            badge.textContent = 'Answered';
                            questionDiv.querySelector('.question-text').appendChild(badge);

                            showToast('Answer saved', 'success');
                        } else {
                            // showToast('Answer updated', 'info');
                        }
                    } else if (data.expired) {
                        showToast('⏰ Test time expired!', 'error');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                })
                .catch(err => {
                    console.error('Save error:', err);
                    showToast('Failed to save answer', 'error');
                });
        }

        // Setup submit button
        function setupSubmitButton() {
            document.getElementById('submitTestBtn').addEventListener('click', function() {
                const attemptedCount = parseInt(document.getElementById('attemptedCount').textContent);
                const totalQuestions = {{ $attempt->total_question }};

                let confirmMessage = 'Are you sure you want to submit the test?';

                if (attemptedCount < totalQuestions) {
                    const unanswered = totalQuestions - attemptedCount;
                    confirmMessage =
                        `You have ${unanswered} unanswered question(s). Are you sure you want to submit?`;
                }

                if (confirm(confirmMessage)) {
                    submitTest();
                }
            });
        }

        // Submit test
        function submitTest() {
            showLoading();

            const submitBtn = document.getElementById('submitTestBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

            fetch("{{ route('supervisor.test.submit') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        attempt_id: ATTEMPT_ID
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();

                    if (data.success) {
                        clearInterval(timerInterval);
                        // showToast('✓ Test submitted successfully!', 'success');

                        // setTimeout(() => {
                            window.location.href = "{{ route('supervisor.dashboard') }}";
                        // }, 1500);
                    } else {
                        showToast('Failed to submit: ' + data.message, 'error');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Test';
                    }
                })
                .catch(err => {
                    hideLoading();
                    console.error('Submit error:', err);
                    showToast('An error occurred while submitting', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Test';
                });
        }

        // Auto-submit when time expires
        function autoSubmitTest() {
            showLoading();
            showToast('⏰ Time is up! Submitting automatically...', 'warning');

            fetch("{{ route('supervisor.test.submit') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        attempt_id: ATTEMPT_ID
                    })
                })
                .then(res => res.json())
                .then(data => {
                    hideLoading();
                    showToast('Test submitted!', 'info');

                    setTimeout(() => {
                        window.location.href = "{{ route('supervisor.dashboard') }}";
                    }, 2000);
                })
                .catch(err => {
                    hideLoading();
                    console.error('Auto-submit error:', err);
                    window.location.href = "{{ route('supervisor.dashboard') }}";
                });
        }
        // ==================== SECURITY MEASURES ====================

        // Security Configuration
        // const SECURITY_CONFIG = {
        //     MAX_TAB_SWITCHES: 3,
        //     AUTO_SUBMIT_ON_VIOLATION: false, // Set true to auto-submit
        //     ENABLE_FULLSCREEN: false, // Set true to force fullscreen
        //     DISABLE_RIGHT_CLICK: true,
        //     DISABLE_DEVTOOLS: true,
        //     DISABLE_COPY_PASTE: true,
        //     LOG_VIOLATIONS: true
        // };



        



        // 1. Disable Right Click
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showToast('Right-click is disabled during the test', 'warning');
        });

        // 2. Disable F12 / Developer Tools
        document.addEventListener('keydown', function(e) {
            // F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
            if (e.keyCode === 123 ||
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && e.keyCode === 85)) {
                e.preventDefault();
                showToast('Developer tools are disabled during the test', 'error');
                return false;
            }
        });

        // 3. Detect Tab Switch / Window Blur
        let tabSwitchCount = 0;
        const MAX_TAB_SWITCHES = 3;

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                tabSwitchCount++;

                // Log to server
                logSecurityViolation('tab_switch', tabSwitchCount);

                if (tabSwitchCount >= MAX_TAB_SWITCHES) {
                    // showToast('Warning: Multiple tab switches detected. Test may be auto-submitted', 'error');

                    // Optional: Auto-submit after 3 switches
                    submitTest();
                } else {
                    showToast(
                        `Warning: Tab switching is not allowed. If you switch tabs again, the test will be automatically submitted.`,
                        'warning'
                    );

                }
            } else {
                // Tab became active again - sync time
                syncWithServer();
            }
        });

        // 4. Detect Window Blur (switching to another window)
        window.addEventListener('blur', function() {
            logSecurityViolation('window_blur', null);
            // showToast('Please stay on the test window', 'warning');
        });

        // 5. Disable Copy/Paste
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            showToast('Copying is disabled during the test', 'warning');
        });

        document.addEventListener('paste', function(e) {
            // Allow paste only in text input fields for answers
            // if (!e.target.classList.contains('answer-input')) {
                e.preventDefault();
                showToast('Pasting is disabled', 'warning');
            // }
        });

        document.addEventListener('cut', function(e) {
            e.preventDefault();
            showToast('Cut operation is disabled', 'warning');
        });

        // 6. Disable Text Selection (except in input fields)
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            // if (!e.target.classList.contains('answer-input') &&
            //     e.target.tagName !== 'INPUT' &&
            //     e.target.tagName !== 'TEXTAREA') {
            //     e.preventDefault();
            // }
        });

        // 7. Detect Screen Recording (basic detection)
        if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia) {
            // Monitor for screen sharing
            setInterval(function() {
                if (document.hasFocus() && document.visibilityState === 'visible') {
                    // Test is in focus - good
                    // alert('Screen recording detection is not fully reliable. Please ensure you are not recording the screen during the test.');
                    // showToast('Screen recording detection is not fully reliable. Please ensure you are not recording the screen during the test.', 'warning');
                }
            }, 1000);
        }

        // 8. Prevent Page Refresh (already have beforeunload, but enhance it)
        // window.addEventListener('beforeunload', function(e) {
        //     const remainingSeconds = getRemainingSeconds();
        //     if (remainingSeconds > 0) {
        //         // Log attempt to leave
        //         logSecurityViolation('attempted_refresh', null);

        //         e.preventDefault();
        //         e.returnValue = 'Your test is in progress. Leaving will not stop the timer!';
        //         return e.returnValue;
        //     }
        // });

     

        // 10. Log Security Violations to Server
        function logSecurityViolation(type, data) {
            fetch("{{ route('supervisor.test.log-violation') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    attempt_id: ATTEMPT_ID,
                    violation_type: type,
                    violation_data: data,
                    timestamp: new Date().toISOString()
                })
            }).catch(err => console.error('Logging error:', err));
        }

        // 11. Monitor for Multiple Browser Windows/Tabs
        let isTestWindowActive = true;

        window.addEventListener('focus', function() {
            isTestWindowActive = true;
        });

        window.addEventListener('blur', function() {
            isTestWindowActive = false;
        });

        // 12. Disable Print
        window.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.keyCode === 80) { // Ctrl+P
                e.preventDefault();
                showToast('Printing is disabled during the test', 'warning');
                return false;
            }
        });

        // 13. Warn on low battery (if supported)
        if ('getBattery' in navigator) {
            navigator.getBattery().then(function(battery) {
                battery.addEventListener('levelchange', function() {
                    if (battery.level < 0.15) {
                        showToast('Warning: Low battery! Please connect charger', 'warning');
                    }
                });
            });
        }


        

        // ==================== END SECURITY MEASURES ====================
    </script>
@endsection
