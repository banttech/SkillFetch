@extends('layout.admin.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        .textDisabled {
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        .tag-btn {
            cursor: default !important;
        }

        /* Question Media Styling */
        .question-media { margin:15px 0; border-radius:12px; overflow:hidden; max-width:480px; position:relative; border: 1px solid #e2e8f0; }
        .question-media img   { max-width:100%; display:block; }
        .question-media video { max-width:100%; display:block; background:#000; }
        
        .btn-full-view {
            position: absolute; top: 10px; right: 10px; z-index: 10;
            background: rgba(15, 23, 42, 0.75); color: #fff;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(4px); border-radius: 6px;
            padding: 6px 14px; font-size: 13px; font-weight: 500;
            cursor: pointer; transition: all 0.2s ease;
            display: flex; align-items: center; gap: 8px;
        }
        .btn-full-view:hover { background: rgba(15, 23, 42, 0.95); transform: translateY(-1px); }

        /* Lightbox Modal */
        .lightbox-backdrop {
            display:none; position:fixed; inset:0;
            background:rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px);
            z-index:99999;
            justify-content:center; align-items:center;
            opacity:0; transition:opacity 0.3s ease;
        }
        .lightbox-backdrop.active { display:flex; opacity:1; }
        .lightbox-content {
            position:relative; text-align:center;
            transform:scale(0.95); transition:transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display:flex; justify-content:center; align-items:center;
        }
        .lightbox-backdrop.active .lightbox-content { transform:scale(1); }
        #lightboxMediaContainer { display:flex; justify-content:center; align-items:center; }
        .lightbox-media {
            max-width:90vw; max-height:85vh; border-radius:8px; 
            box-shadow:0 25px 50px -12px rgba(0,0,0,0.6);
            object-fit:contain;
        }
        .lightbox-media.is-video { width:80vw; max-width:1000px; }
      .btn-lightbox-close {
    position: fixed;
    top: -12px;
    right: -11px;
    background: rgb(195 0 0);
    border: none;
    color: #fff;
    font-size: 23px;
    width: 31px;
    height: 31px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
    transition: all 0.2s ease;
    z-index: 100000;
}
        .btn-lightbox-close:hover { background:rgba(239, 68, 68, 0.9); transform:rotate(90deg); }


      .tab-alert-box.tab-blue-switces .tab-alert-header {
    padding: 14px 18px;
    border-bottom: 1.5px solid #3498db;
    background: #3498db;
}
    .tab-alert-box.tab-blue-switces .tab-alert-title {
    color: #fff;
}      .tab-alert-box.tab-blue-switces .tab-alert-table thead tr {
    background:#b4e1ff;
} .tab-alert-box.tab-blue-switces .tab-alert-table th {
    color: #000000;    border-bottom: 1.5px solid #3498db;}

  .tab-alert-box.tab-blue-switces  .tab-alert-table td:first-child {
    color: #0f6097;
}  .tab-alert-box.tab-blue-switces .tab-alert-table tbody tr {
    border-bottom: 1px solid #3498db;
}.tab-alert-box.tab-blue-switces .tab-alert-table tbody tr:hover{
    background: aliceblue;
}.tab-alert-table td {
    font-weight: 500;
}.tab-alert-box.tab-blue-switces  .tab-alert-time i{
        color: #0f6097;
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
                    <label>Name</label>
                    <p>{{ $name }}</p>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <p>{{ $email }}</p>
                </div>

            </div>

            <div class="detail-row">
                <div class="detail-item">
                    <label>Mobile No.</label>
                    <p>{{ $mobile }}</p>
                </div>
                <div class="detail-item">
                    <label>Test Submission Time</label>
                    <p>{{ $test_date }}</p>
                </div>

                <div class="detail-item">
                    <label>Submission Type</label>
                    <p>{{ ucfirst($attempt->test_submission_status) }}</p>
                </div>
                <div class="detail-item">
                    <label>Time Taken</label>
                    <p>{{ $attempt->formatted_duration }}</p>
                </div>
            </div>

            @if ($attempt->status == 'underReview')
            @elseif($attempt->status == 'pass')
                <div class="attemp passed-attempt">
                    <h4>Passed!!</h4>
                    <p>The supervisor has passed this test attempt.</p>


                </div>
            @elseif($attempt->status == 'fail')
                <div class="rehect-test">
                    <h4> Failed!!</h4>
                    <p> The supervisor has failed this test attempt.</p>
                    <p class="reason-set">
                        @if ($attempt->reject_reason)
                            <strong>Reason:</strong> {{ $attempt->reject_reason }}
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Tab Switch Activity (only shown if any switches occurred) --}}
        @if (isset($tabSwitches) && $tabSwitches->count() > 0)
            <div class="tab-alert-box">
                <div class="tab-alert-header">
                    <div class="tab-alert-header-left">
                        <i class="fas fa-exclamation-triangle tab-alert-icon"></i>
                        <strong class="tab-alert-title">Tab Switch Activity</strong>
                    </div>
                    <span class="tab-alert-badge">
                        {{ $tabSwitches->count() }} switch{{ $tabSwitches->count() > 1 ? 'es' : '' }} detected
                    </span>
                </div>

                <div class="tab-alert-table-wrap">
                    <table class="tab-alert-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Switched At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tabSwitches as $i => $sw)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <span class="tab-alert-time">
                                            <i class="fas fa-clock"></i>
                                            {{ $sw->switched_at->format('d M Y, h:i:s A') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Camera Activity --}}
        @if (isset($cameraActivities) && $cameraActivities->count() > 0)
            <div class="tab-alert-box tab-blue-switces" style="margin-top: 20px; border-left: 4px solid #3498db; background:aliceblue;">
                <div class="tab-alert-header">
                    <div class="tab-alert-header-left">
                        <i class="fas fa-camera tab-alert-icon" style="color: #fff;"></i>
                        <strong class="tab-alert-title">Camera Activity</strong>
                    </div>
                    <span class="tab-alert-badge" style="background-color: #fff;">
                        {{ $cameraActivities->count() }} event{{ $cameraActivities->count() > 1 ? 's' : '' }} detected
                    </span>
                </div>

                <div class="tab-alert-table-wrap">
                    <table class="tab-alert-table">
                        <thead>
                            <tr>
                                <th style="min-width: 30px;">#</th>
                                <th style="min-width: 50px;">Event Type</th>
                                <th style="min-width: 200px">Details</th>
                                <th style="min-width: 200px;">Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cameraActivities as $i => $ca)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        @php
                                            $badgeClass = match ($ca->activity_type) {
                                                'working' => 'bg-success',
                                                'error' => 'bg-danger',
                                                'camera_off' => 'bg-warning',
                                                'hidden' => 'bg-dark',
                                                'permission_denied' => 'bg-danger',
                                                default => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} text-white">
                                            {{ ucfirst(str_replace('_', ' ', $ca->activity_type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $ca->details }}</td>
                                    <td>
                                        <span class="tab-alert-time">
                                            <i class="fas fa-clock"></i>
                                            {{ $ca->occurred_at->format('d M Y, h:i:s A') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Questions Section --}}
        <div class="div-inner-sections">

            @php $questionNumber = 1; @endphp

            @foreach ($groupedQuestions as $departmentName => $questions)
                <h3 class="department-heading">
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

                                    {{-- Media Display (Image/Video) --}}
                                    @if($attemptedQuestion->questionModel?->media_path)
                                        <div class="question-media">
                                            @php
                                                $mediaType = $attemptedQuestion->questionModel->answer_type; // 'image' or 'video' in Question table
                                                $mediaUrl = asset('storage/' . $attemptedQuestion->questionModel->media_path);
                                            @endphp
                                            <button type="button" class="btn-full-view" onclick="openLightbox('{{ $mediaUrl }}', '{{ $mediaType }}')">
                                                <i class="fas fa-expand"></i> Full View
                                            </button>
                                            
                                            @if($mediaType === 'image')
                                                <img src="{{ $mediaUrl }}" alt="Question Image">
                                            @elseif($mediaType === 'video')
                                                <video controls><source src="{{ $mediaUrl }}"></video>
                                            @endif
                                        </div>
                                    @endif

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
                                                <strong>User's Answer:</strong> <span style="color: red">Not
                                                    Attempted</span>
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
                                                <strong>User's Answer:</strong> <span style="color: red">Not
                                                    Attempted</span>
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
                    <span><input type="text" class="score-input" id="currentScore" value="{{ $currentScore }}" readonly>
                        <span class="score-total">/ {{ $attempt->total_marks }}</span></span>
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
                <form id="reviewForm" method="POST" class="skill-suggested">

                    @csrf
                    <div class="">
                        <label class="form-label">Suggested Skills by Admin <span>(Optional)</span></label><br>
                        <select class="form-control select2" name="suggested_skills[]" id="suggestedSkills" multiple>
                            @if (isset($allSkills))
                                @foreach ($allSkills as $skillObj)
                                    <option value="{{ $skillObj->id }}">{{ $skillObj->name }}</option>
                                @endforeach
                            @endif
                        </select>

                    </div>
                    <div class="action-buttons">
                        <button type="submit" class="btn-verify" id="verifyBtn"
                            formaction="{{ route('admin.test-review.verify', $attempt->id) }}"
                            {{ $currentScore < $attempt->passing_marks ? 'disabled' : '' }}
                            onclick="return confirm('Are you sure you want to verify this test? This action will mark the supervisor as pass.');">
                            <img src="{{ asset('admin_assets/images/double-tick.png') }}" alt="verify">
                            Verify (Pass)
                        </button>

                        <button type="button" class="btn-reject" id="rejectBtn" data-toggle="modal"
                            data-target="#rejectModal" {{ $currentScore >= $attempt->passing_marks ? 'disabled' : '' }}>
                            <img src="{{ asset('admin_assets/images/Close.png') }}" alt="reject">
                            Reject (Fail)
                        </button>
                    </div>
                </form>
            @elseif($attempt->status == 'fail')
                <div class="rehect-test">
                    <h4> Failed!!</h4>
                    <p> The supervisor has failed this test attempt.</p>
                    <p class="reason-set">
                        @if ($attempt->reject_reason)
                            <strong>Reason:</strong> {{ $attempt->reject_reason }}
                        @endif
                    </p>
                    @if ($attempt->testSkills && $attempt->testSkills->count() > 0)
                        <div class="div-skills"><strong>Suggested Skills by Admin:</strong>
                            @foreach ($attempt->testSkills as $ts)
                                <span class="badge"
                                    style="    background-color: #00466d;
    color: white;
    margin-right: 5px;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 600;
    letter-spacing: .3px;">{{ $ts->skill->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @elseif($attempt->status == 'pass')
                <div class="attemp passed-attempt">
                    <h4>Passed!!</h4>
                    <p>The supervisor has passed this test attempt.</p>
                    @if ($attempt->testSkills && $attempt->testSkills->count() > 0)
                        <div class="div-skills">
                            <strong>Suggested Skills by Admin:</strong>
                            @foreach ($attempt->testSkills as $ts)
                                <span class="badge"
                                    style="    background-color: #00466d;
    color: white;
    margin-right: 5px;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: 600;
    letter-spacing: .3px;">{{ $ts->skill->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.test-review.reject', $attempt->id) }}" method="POST" id="rejectForm"
                    onsubmit="copySkills()">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel" style="font-size: 1.25rem;">Reject Test Attempt
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label" style="font-weight: 500;"><span>Reason for
                                    Rejection<span style="color: red">*</span></span></label>

                            <textarea class="form-control" id="rejectReason" name="reject_reason" rows="4" required
                                placeholder="Enter the reason for rejection..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lightbox Backdrop -->
    <div class="lightbox-backdrop" id="lightboxBackdrop" onclick="closeLightbox(event)">
        <div class="lightbox-content">
            <button class="btn-lightbox-close" onclick="closeLightbox(event)">&times;</button>
            <div id="lightboxMediaContainer"></div>
        </div>
    </div>

    <!-- SELECT2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ── Lightbox Logic ──────────────────────────────────────────────────────────
        function openLightbox(mediaUrl, type) {
            const container = document.getElementById('lightboxMediaContainer');
            if (type === 'image') {
                container.innerHTML = `<img src="${mediaUrl}" class="lightbox-media" alt="Full View">`;
            } else if (type === 'video') {
                container.innerHTML = `<video controls autoplay class="lightbox-media is-video"><source src="${mediaUrl}"></video>`;
            }
            document.getElementById('lightboxBackdrop').classList.add('active');
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }

        function closeLightbox(e = null) {
            if (e && e.target !== document.getElementById('lightboxBackdrop') && !e.target.classList.contains('btn-lightbox-close')) {
                return; 
            }
            document.getElementById('lightboxBackdrop').classList.remove('active');
            document.getElementById('lightboxMediaContainer').innerHTML = ''; 
            document.body.style.overflow = ''; // Restore scrolling
        }

        const PASSING_MARKS = {{ $attempt->passing_marks }};
        const PER_QUESTION_MARKS = {{ $attempt->per_question_marks }};
        const TOTAL_MARKS = {{ $attempt->total_marks }};

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2
            $('#suggestedSkills').select2({
                placeholder: "Select skills",
                allowClear: true,
                width: '100%'
            });
            //  $('#suggestedSkills').select2({
            //      width: '100%'
            //  });
            // Setup correction checkbox listeners
            document.querySelectorAll('.correction-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    toggleCorrection(this);
                });
            });
        });

        function copySkills() {
            const mainSelect = document.getElementById('suggestedSkills');
            const rejectForm = document.getElementById('rejectForm');

            if (!mainSelect || !rejectForm) return;

            // Remove old hidden inputs
            rejectForm.querySelectorAll('.copied-skill').forEach(el => el.remove());

            for (let option of mainSelect.options) {
                if (option.selected) {
                    let hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'suggested_skills[]';
                    hidden.value = option.value;
                    hidden.className = 'copied-skill';
                    rejectForm.appendChild(hidden);
                }
            }
        }

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
