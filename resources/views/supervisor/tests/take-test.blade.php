@extends('layout.supervisor.app')

@section('content')
<style>
    @keyframes spin    { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }
    @keyframes pulse   { 0%,100%{opacity:1} 50%{opacity:.6} }
    @keyframes fadeIn  { from{opacity:0;transform:scale(.8)} to{opacity:1;transform:scale(1)} }

    .loading-overlay {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,.7); z-index:9999;
        justify-content:center; align-items:center;
    }
    .loading-overlay.active { display:flex; }
    .loading-spinner { text-align:center; color:#fff; }
    .loading-spinner .spinner {
        border:5px solid #f3f3f3; border-top:5px solid #3498db;
        border-radius:50%; width:60px; height:60px;
        animation:spin 1s linear infinite; margin:0 auto 15px;
    }
    .timer-critical { animation:pulse 1s infinite; }
    .badge-animated { animation:fadeIn .3s ease-in; }

    .question-media { margin:10px 0 14px; border-radius:10px; overflow:hidden; max-width:420px; }
    .question-media img   { max-width:100%; border-radius:10px; border:1px solid #e2e8f0;    height: 207px;object-fit: cover; }
    .question-media video { max-width:100%; border-radius:10px; border:1px solid #e2e8f0;     height: 207px;}
span.tebs-switch {
    background: #fef0d6;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 7px 12px;
    line-height: 19px;
    font-size: 13px;
    border: 1px solid #f29e00;
    border-radius: 5px;
    color: #6b4000;
    font-weight: 500;
    letter-spacing: .2px;
}
    /* Warning Modal */
    .warn-modal-backdrop {
        display:none; position:fixed; inset:0;
        background:rgba(0,0,0,.55); z-index:10000;
        justify-content:center; align-items:center;
    }
    .warn-modal-backdrop.active { display:flex; }
    .warn-modal {
        background:#fff; border-radius:16px;
        padding:32px 28px; max-width:420px; width:90%;
        text-align:center; box-shadow:0 20px 60px rgba(0,0,0,.25);
    }
    .warn-modal .wm-icon {
        width:64px; height:64px; border-radius:50%;
        display:flex; align-items:center; justify-content:center; margin:0 auto 16px;
    }
    .wm-icon-warn   { background:#fff3cd; }
    .wm-icon-warn i { color:#f59e0b; font-size:28px; }
    .wm-icon-danger   { background:#fee2e2; }
    .wm-icon-danger i { color:#ef4444; font-size:28px; }
   .warn-modal h4 {
    font-size: 19px;
    font-weight: 600;
    color: #c30f0f;
    margin: 0 0 10px;
}
   .warn-modal p {
    font-size: 14px;
    color: #2a2a2a;
    line-height: 1.6;
    margin: 0 0 24px;
    text-align: center;
    letter-spacing: .2px;
}
   .btn-wm-ok {
    background: linear-gradient(90deg, #00466D, #0E253A);
    color: #fff;
    border: none;
    padding: 10px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    letter-spacing: .4px;
}
    .btn-wm-ok:hover { opacity:1; }

    /* Lightbox Modal */
    .lightbox-backdrop {
        display:none; position:fixed; inset:0;
        background:rgba(15, 23, 42, 0.85); backdrop-filter: blur(5px);
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
        border:2px solid rgba(255,255,255,0.05);
        object-fit:contain;
    }
    .lightbox-media.is-video { 
        width:80vw; max-width:1000px;
        background:#000; outline:none; 
    }
span.submit-test {
    background: #e4ffe5b8;
    padding: 6px 9px;
    border: 1px solid #007104;
    border-radius: 5px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #004900;
    font-weight: 500;
    margin-top: 11px;
    text-align: center;
    font-size: 13px;
}
.btn-lightbox-close
 {
    position: fixed;
    top: -16px;
    right: -16px;
    background: rgba(0, 0, 0, 0.5);
    border: none;
    color: #fff;
    font-size: 24px;
    width: 41px;
    height: 41px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(4px);
    transition: all 0.2s ease;
    z-index: 100000;
}
    .btn-lightbox-close:hover { background:rgba(239, 68, 68, 0.9); transform:scale(1.1); }

    .btn-full-view {
        position: absolute; top: 8px; right: 8px; z-index: 10;
        background: rgba(15, 23, 42, 0.75); color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
        backdrop-filter: blur(4px); border-radius: 6px;
        padding: 5px 12px; font-size: 13px; font-weight: 500;
        cursor: pointer; transition: all 0.2s ease;
        display: flex; align-items: center; gap: 6px;
    }
    .btn-full-view:hover { background: rgba(15, 23, 42, 0.95); transform: translateY(-1px); }

    /* Camera Preview Styling */
    .camera-container {
        position: fixed; bottom: 30px; left: 30px;
        width: 240px; height: 180px; z-index: 999;
        border-radius: 20px; overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        border: 2px solid rgba(255,255,255,0.2);
        background: #0f172a;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .camera-container:hover { transform: scale(1.05); }
    
    #cameraPreview {
        width: 100%; height: 100%; object-fit: cover;
        transform: scaleX(-1); /* Mirror effect */
    }
    
    .camera-status-overlay {
        position: absolute; top: 12px; left: 12px;
        display: flex; align-items: center; gap: 6px;
        padding: 4px 10px; border-radius: 20px;
        background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
        z-index: 10;
    }
    .status-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #10b981; box-shadow: 0 0 10px #10b981;
    }
    .status-text {
        color: #fff; font-size: 11px; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    
    .camera-error-placeholder {
        display: none; position: absolute; inset: 0;
        flex-direction: column; align-items: center; justify-content: center;
        padding: 20px; text-align: center; color: #fff;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    }
    .camera-error-placeholder i { color: #ef4444; font-size: 32px; margin-bottom: 12px; }
    .camera-error-placeholder span { font-size: 13px; font-weight: 500; line-height: 1.4; }
    
    .camera-container.has-error .camera-error-placeholder { display: flex; }
    .camera-container.has-error #cameraPreview { display: none; }
    .camera-container.has-error .camera-status-overlay .status-dot { background: #ef4444; box-shadow: 0 0 10px #ef4444; }

    @media(max-width:450px){
        .warn-modal p {
    font-size: 13px;
}.btn-lightbox-close {
    font-size: 18px;
    width: 28px;
    height: 28px;
}.camera-error-placeholder span {
    font-size: 0;
}.camera-container {
    position: fixed;
    bottom: 0;
    left: auto;
    width: 92px;
    height: 98px;
    right: 9px;
    z-index: 999;
    top: 14px;
}.camera-status-overlay {
    top: 5px;
    left: 12px;
    padding: 0px 10px;
}.camera-error-placeholder i {
    font-size: 25px;
    margin-bottom: 0;
}.status-text {
    font-size: 9px;
}.btn-wm-ok {
    width: 100%;
}
    }
</style>

<div class="content">
    <div class="container-fluid">

        <div class="camera-container" id="cameraContainer">
            <div class="camera-status-overlay">
                <div class="status-dot"></div>
                <span class="status-text" id="cameraStatusText">Live Feed</span>
            </div>
            <video id="cameraPreview" autoplay muted playsinline></video>
            <canvas id="cameraCanvas" style="display:none;"></canvas>
            <div class="camera-error-placeholder">
                <i class="fas fa-video-slash"></i>
                <span id="cameraErrorMessage"><b>Camera Error:</b> Access Denied</span>
            </div>
        </div>

        <div class="loading-overlay" id="loadingOverlay">
            <div class="loading-spinner"><div class="spinner"></div><h4>Processing...</h4></div>
        </div>

        <div class="warn-modal-backdrop" id="warnModalBackdrop">
            <div class="warn-modal">
                <div class="wm-icon" id="warnModalIcon">
                    <i id="warnModalIconI" class="fas fa-exclamation-triangle"></i>
                </div>
                <h4 id="warnModalTitle">Warning</h4>
                <p id="warnModalMsg"></p>
                <button class="btn-wm-ok" onclick="closeWarnModal()">OK, I Understand</button>
            </div>
        </div>

        <div class="lightbox-backdrop" id="lightboxBackdrop" onclick="closeLightbox(event)">
            <div class="lightbox-content">
                <button class="btn-lightbox-close" onclick="closeLightbox(event)">&times;</button>
                <div id="lightboxMediaContainer"></div>
            </div>
        </div>

        {{-- TEST HEADER --}}
        <div class="row">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="test-name-box">
                    <p><span class="label">Test Name:</span><span class="ms-2">{{ $attempt->test_name }}</span></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="questions-box">
                    <p class="div-top-ques2">No. of Questions: {{ $attempt->total_question }}</p><br>
                    <p class="div-top-ques3">Passing Marks: {{ $attempt->passing_marks }}</p>
                    <p class="marks-info">Questions carry varying marks by type</p>
                </div>
            </div>
        </div>

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

        <form id="testForm">
            @csrf
            <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">

            @foreach ($groupedQuestions as $departmentName => $questions)
                <div class="question-card">
                    <h2 class="department-title">Department: {{ $departmentName }}</h2>

                    @foreach ($questions as $index => $attemptedQuestion)
                        <div class="question-item" data-question-id="{{ $attemptedQuestion->question_id }}">

                            <p class="question-text">
                                {{ $loop->parent->iteration }}.{{ $loop->iteration }}.
                                {{ $attemptedQuestion->question }}
                                @if($attemptedQuestion->is_attempted)
                                    <span class="badge bg-success ms-2 badge-animated">Answered</span>
                                @endif
                                @if(isset($attemptedQuestion->marks_per_question) && $attemptedQuestion->marks_per_question > 0)
                                    <span class="badge bg-secondary ms-1" style="font-size:11px;">
                                        {{ $attemptedQuestion->marks_per_question }} mark{{ $attemptedQuestion->marks_per_question > 1 ? 's' : '' }}
                                    </span>
                                @endif
                            </p>

                            @if($attemptedQuestion->questionModel?->media_path)
                                <div class="question-media" style="position:relative; width: fit-content;">
                                    <button type="button" class="btn-full-view" onclick="openLightbox('{{ asset('storage/' . $attemptedQuestion->questionModel->media_path) }}', '{{ $attemptedQuestion->questionModel->answer_type }}')">
                                        <i class="fas fa-expand"></i> Full View
                                    </button>
                                    @if($attemptedQuestion->questionModel->answer_type === 'image')
                                        <img src="{{ asset('storage/' . $attemptedQuestion->questionModel->media_path) }}" alt="Question Image">
                                    @elseif($attemptedQuestion->questionModel->answer_type === 'video')
                                        <video controls><source src="{{ asset('storage/' . $attemptedQuestion->questionModel->media_path) }}"></video>
                                    @endif
                                </div>
                            @endif

                            @if($attemptedQuestion->answer_type === 'text')
                                @php $userAnswer = $attemptedQuestion->attemptedAnswers->first()?->user_answer ?? ''; @endphp
                                <input type="text" class="text-input answer-input" placeholder="Write your answer..."
                                    data-question-id="{{ $attemptedQuestion->question_id }}" data-answer-type="text"
                                    value="{{ $userAnswer }}">
                            @endif

                            @if($attemptedQuestion->answer_type === 'multi')
                                <div class="checkbox-group">
                                    @foreach($attemptedQuestion->attemptedAnswers as $option)
                                        <div class="checkbox-option">
                                            <input type="checkbox" id="opt{{ $option->id }}" class="answer-input"
                                                data-question-id="{{ $attemptedQuestion->question_id }}" data-answer-type="multi"
                                                data-answer-id="{{ $option->answer_id }}" value="{{ $option->answer_id }}"
                                                {{ $option->user_answer == 1 ? 'checked' : '' }}>
                                            <label for="opt{{ $option->id }}">{{ $option->answer }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($attemptedQuestion->answer_type === 'single')
                                <div class="radio-group">
                                    @foreach($attemptedQuestion->attemptedAnswers as $option)
                                        <div class="radio-option">
                                            <input type="radio" id="opt{{ $option->id }}" class="answer-input"
                                                data-question-id="{{ $attemptedQuestion->question_id }}" data-answer-type="single"
                                                name="question_{{ $attemptedQuestion->question_id }}" value="{{ $option->answer_id }}"
                                                {{ $option->user_answer == 1 ? 'checked' : '' }}>
                                            <label for="opt{{ $option->id }}">{{ $option->answer }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        </div>
                    @endforeach


          

                </div>
            @endforeach
               <div class="submit-section">
                <button type="button" class="submit-btn" id="submitTestBtn">
                    <i class="fas fa-paper-plane"></i> Submit Test
                </button>
            </div>
       
        </form>

    </div>
</div>

<script>
    const ATTEMPT_ID    = {{ $attempt->id }};
    const CSRF_TOKEN    = "{{ csrf_token() }}";
    const END_TIME      = new Date("{{ $attempt->end_time->toIso8601String() }}").getTime();
    const TOTAL_Q       = {{ $attempt->total_question }};
    const REDIRECT_URL  = "{{ route('supervisor.tests.show', $attempt->test_id ?? 0) }}";

    let timerInterval;
    let notified15Min  = false;
    let notified2Min   = false;
    let tabSwitchCount = {{ $attempt->tabSwitches->count() }};
    let isAutoSubmitting = false;

    const MAX_TAB_SWITCHES = {{ \App\Models\SupervisorTestAttempt::MAX_TAB_SWITCHES }};

    // Immediate lockdown check on page load
    if (tabSwitchCount > MAX_TAB_SWITCHES) {
        isAutoSubmitting = true;
        showWarnModal(
            'Test Auto-Submitted!',
            '<span class="tabs-swtiecs"><span class="tebs-switch">You have exceeded the maximum of 2 tab switches.</span> <span class="submit-test">The test has been submitted automatically.</span></span>',
            'danger'
        );
       autoSubmitTest();
    }

    // ── Modal ──────────────────────────────────────────────────────────────────
    function showWarnModal(title, msg, type = 'warn', onOk = null) {
        closeLightbox(); // Close lightbox automatically if a warning appears

        const backdrop = document.getElementById('warnModalBackdrop');
        const titleEl = document.getElementById('warnModalTitle');
        const msgEl = document.getElementById('warnModalMsg');
        const icon = document.getElementById('warnModalIcon');
        const iconI = document.getElementById('warnModalIconI');

        if (backdrop.classList.contains('active')) {
            // If already showing a warning, just append to it so user sees all issues
            msgEl.innerHTML += '<br><span style="color:#c30f0f; font-weight:bold;">Additional Issue:</span> <br>' + msg;
            return;
        }

        titleEl.textContent = title;
        msgEl.innerHTML   = msg;

        if (type === 'danger') {
            icon.className  = 'wm-icon wm-icon-danger';
            iconI.className = 'fas fa-times-circle';
        } else {
            icon.className  = 'wm-icon wm-icon-warn';
            iconI.className = 'fas fa-exclamation-triangle';
        }
        window._warnModalOnOk = onOk;
        backdrop.classList.add('active');
    }
    function closeWarnModal() {
        document.getElementById('warnModalBackdrop').classList.remove('active');
        if (typeof window._warnModalOnOk === 'function') {
            window._warnModalOnOk();
            window._warnModalOnOk = null;
        }
    }

    // ── Lightbox ───────────────────────────────────────────────────────────────
    function openLightbox(mediaUrl, type) {
        const container = document.getElementById('lightboxMediaContainer');
        if (type === 'image') {
            container.innerHTML = `<img src="${mediaUrl}" class="lightbox-media" alt="Full View">`;
        } else if (type === 'video') {
            container.innerHTML = `<video controls autoplay class="lightbox-media is-video"><source src="${mediaUrl}"></video>`;
        }
        document.getElementById('lightboxBackdrop').classList.add('active');
    }

    function closeLightbox(e = null) {
        if (e && e.target !== document.getElementById('lightboxBackdrop') && !e.target.classList.contains('btn-lightbox-close')) {
            return; // only close if clicking backdrop or close button
        }
        document.getElementById('lightboxBackdrop').classList.remove('active');
        document.getElementById('lightboxMediaContainer').innerHTML = ''; // clear video/audio
    }

    // ── Init ───────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
        startRealTimeTimer();
        setupAnswerListeners();
        setupSubmitButton();
        startCameraMonitoring();
        setInterval(syncWithServer, 30000);
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) syncWithServer();
        });
    });

    // ── Camera Monitoring ──────────────────────────────────────────────────────
    let cameraStream = null;
    let cameraTrack = null;
    let lastKnownStatus = null; 
    let monitorInterval = null;
    let consecutiveDarkFrames = 0;
    const DARK_THRESHOLD = 75; // Increased to 75 to easily detect hand covering

    async function startCameraMonitoring() {
        const container = document.getElementById('cameraContainer');
        const statusText = document.getElementById('cameraStatusText');

        try {
            // If already have a stream, check if it's dead before asking for a new one
            if (cameraTrack && cameraTrack.readyState === 'live' && cameraTrack.enabled) {
                return; 
            }

            cameraStream = await navigator.mediaDevices.getUserMedia({ video: true });
            
            const preview = document.getElementById('cameraPreview');
            if (preview) {
                preview.srcObject = cameraStream;
                preview.onplay = () => {
                    // Ensure it stays removal from error state if it recovers
                    if (lastKnownStatus !== 'working') {
                         logCameraActivity('working', 'Camera feed successfully started');
                         lastKnownStatus = 'working';
                         container.classList.remove('has-error');
                         statusText.textContent = 'Live Feed';
                    }
                };
            }

            cameraTrack = cameraStream.getVideoTracks()[0];
            
            cameraTrack.onended = () => {
                handleViolation('camera_off', 'Camera hardware/software was turned off or disconnected');
            };

            // Start monitoring loop
            if (!monitorInterval) {
                monitorInterval = setInterval(performHealthAndBrightnessCheck, 3000);
            }

        } catch (err) {
            handleCameraError(err);
        }
    }

    async function performHealthAndBrightnessCheck() {
        const video = document.getElementById('cameraPreview');
        const canvas = document.getElementById('cameraCanvas');
        const container = document.getElementById('cameraContainer');
        const statusText = document.getElementById('cameraStatusText');

        // 1. Check Technical Health
        if (!cameraStream || !cameraTrack || cameraTrack.readyState !== 'live' || !cameraTrack.enabled) {
            handleViolation('camera_off', 'Camera state became inactive or was disabled via keyboard/system');
            startCameraMonitoring(); // Attempt to restore
            return;
        }

        // 2. Check Playback
        if (video.paused || video.ended) {
            handleViolation('camera_off', 'Camera playback was stopped or paused by the user');
            video.play().catch(() => {});
            return;
        }

        // 3. Analyze Brightness (Shutter/Hand)
        try {
            const ctx = canvas.getContext('2d', { willReadFrequently: true });
            canvas.width = 40;
            canvas.height = 30;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            let totalBrightness = 0;

            for (let i = 0; i < data.length; i += 4) {
                totalBrightness += (data[i] + data[i+1] + data[i+2]) / 3;
            }

            const avgBrightness = totalBrightness / (data.length / 4);

            if (avgBrightness < DARK_THRESHOLD) {
                consecutiveDarkFrames++;
                if (consecutiveDarkFrames >= 2) { 
                    handleViolation('hidden', 'Camera covered, shutter closed, or very dark environment detected');
                }
            } else {
                // RECOVERY
                if (lastKnownStatus !== 'working' && lastKnownStatus !== null) {
                    logCameraActivity('working', 'Camera feed recovered and visibility restored');
                    lastKnownStatus = 'working';
                    container.classList.remove('has-error');
                    statusText.textContent = 'Live Feed';
                }
                consecutiveDarkFrames = 0;
            }
        } catch (e) {
            console.error('Frame analysis failed', e);
        }
    }

    function handleViolation(type, msg) {
        if (lastKnownStatus === type) return;

        logCameraActivity(type, msg);
        lastKnownStatus = type;
        
        updateCameraUIError(type === 'hidden' ? 'Camera Covered/Dark' : 'Camera Disconnected');
        showWarnModal(
            type === 'hidden' ? 'Camera Covered!' : 'Camera Issue!',
            msg + '. Please fix this immediately to avoid test rejection.',
            'danger'
        );
    }

    function handleCameraError(err) {
        let displayMsg = 'Camera Error: ' + err.message;
        let type = 'error';

        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            type = 'permission_denied';
            displayMsg = 'Camera Access Denied';
        }

        if (lastKnownStatus !== type) {
            logCameraActivity(type, 'Failed to access camera: ' + err.message);
            lastKnownStatus = type;
            
            showWarnModal(
                'Camera Issue Detected!',
                displayMsg + '. Please ensure your camera is connected and permitted. Repeated failures may lead to test rejection.',
                'danger'
            );
        }

        updateCameraUIError(displayMsg);
        setTimeout(startCameraMonitoring, 10000); 
    }

    function updateCameraUIError(msg) {
        const container = document.getElementById('cameraContainer');
        const statusText = document.getElementById('cameraStatusText');
        const errorMessage = document.getElementById('cameraErrorMessage');
        
        container.classList.add('has-error');
        statusText.textContent = 'Issue';
        errorMessage.textContent = msg;
    }

    function logCameraActivity(type, details) {
        fetch("{{ route('supervisor.test.log-camera-activity') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID, activity_type:type, details:details})
        }).catch(console.error);
    }

    // ── Timer ──────────────────────────────────────────────────────────────────
    function startRealTimeTimer() {
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            updateTimerDisplay();
            const rem = getRemainingSeconds();
            if (rem === 900 && !notified15Min) {
                notified15Min = true;
                showWarnModal('15 Minutes Remaining', 'You have 15 minutes left. Please review your answers.', 'warn');
            }
            if (rem === 120 && !notified2Min) {
                notified2Min = true;
                document.getElementById('timerDisplay').classList.add('timer-critical');
                showWarnModal('Only 2 Minutes Left!', 'Time is running out! The test will be submitted automatically when the timer reaches zero.', 'danger');
            }
            if (rem <= 0) { clearInterval(timerInterval); autoSubmitTest(); }
        }, 1000);
    }
    function getRemainingSeconds() {
        return Math.max(0, Math.floor((END_TIME - Date.now()) / 1000));
    }
    function updateTimerDisplay() {
        const rem = getRemainingSeconds();
        const m = Math.floor(rem / 60), s = rem % 60;
        document.getElementById('timeRemaining').textContent = `${m} min ${String(s).padStart(2,'0')} sec`;
        const el = document.getElementById('timerDisplay');
        el.style.color = rem <= 120 ? 'red' : rem <= 900 ? 'orange' : '';
    }
    function syncWithServer() {
        fetch("{{ route('supervisor.test.remaining-time') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID})
        }).then(r=>r.json()).then(d=>{ if(d.expired){clearInterval(timerInterval);autoSubmitTest();} }).catch(console.error);
    }

    // ── Answers ────────────────────────────────────────────────────────────────
    function setupAnswerListeners() {
        document.querySelectorAll('.answer-input[data-answer-type="text"]').forEach(input => {
            let t;
            input.addEventListener('input', function(){ clearTimeout(t); t=setTimeout(()=>{if(this.value.trim())saveAnswer(this.dataset.questionId,this.value,'text');},1000); });
            input.addEventListener('blur',  function(){ if(this.value.trim())saveAnswer(this.dataset.questionId,this.value,'text'); });
        });
        document.querySelectorAll('.answer-input[data-answer-type="single"]').forEach(input=>{
            input.addEventListener('change', function(){ saveAnswer(this.dataset.questionId,this.value,'single'); });
        });
        document.querySelectorAll('.answer-input[data-answer-type="multi"]').forEach(input=>{
            input.addEventListener('change', function(){
                const qId=this.dataset.questionId;
                const values=Array.from(document.querySelectorAll(`.answer-input[data-question-id="${qId}"][data-answer-type="multi"]:checked`)).map(c=>c.value);
                if(values.length>0)saveAnswer(qId,values,'multi');
            });
        });
    }
    function saveAnswer(questionId, answer, type) {
        fetch("{{ route('supervisor.test.save-answer') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID,question_id:questionId,answer})
        }).then(r=>r.json()).then(data=>{
            if(data.success) {
                document.getElementById('attemptedCount').textContent = data.attempted_count;
                const qDiv=document.querySelector(`[data-question-id="${questionId}"]`);
                if(qDiv&&!qDiv.querySelector('.badge.bg-success')) {
                    const b=document.createElement('span'); b.className='badge bg-success ms-2 badge-animated'; b.textContent='Answered';
                    qDiv.querySelector('.question-text').appendChild(b);
                }
            } else if(data.expired) {
                showWarnModal('Time Expired','Your test time has expired. The test will be submitted now.','danger',()=>window.location.reload());
            }
        }).catch(()=>{});
    }

    // ── Submit ─────────────────────────────────────────────────────────────────
    function setupSubmitButton() {
        document.getElementById('submitTestBtn').addEventListener('click', function() {
            const attempted  = parseInt(document.getElementById('attemptedCount').textContent);
            const unanswered = TOTAL_Q - attempted;
            const msg = unanswered > 0
                ? `You have ${unanswered} unanswered question(s). Are you sure you want to submit the test?`
                : 'Are you sure you want to submit the test?';
            if(confirm(msg)) submitTest('supervisor');
        });
    }

    function submitTest(submissionType = 'supervisor') {
        showLoading();
        const btn = document.getElementById('submitTestBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

        fetch("{{ route('supervisor.test.submit') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID, submission_type: submissionType})
        }).then(r=>r.json()).then(data=>{
            hideLoading();
            if(data.success) { clearInterval(timerInterval); window.location.href=REDIRECT_URL; }
            else {
                if(window.toastr) toastr.error('Failed to submit: ' + data.message);
                btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane"></i> Submit Test';
            }
        }).catch(()=>{ hideLoading(); btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane"></i> Submit Test'; });
    }

    function autoSubmitTest() {
        // showLoading();
       // showWarnModal('Time Is Up!','Your test time has expired. The test is being submitted automatically now.','danger');
        fetch("{{ route('supervisor.test.submit') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID, submission_type:'automatic'})
        }).then(r=>r.json()).then(()=>{ hideLoading(); window.location.href=REDIRECT_URL; })
         .catch(()=>{ hideLoading(); window.location.href=REDIRECT_URL; });
    }

    function showLoading()  { document.getElementById('loadingOverlay').classList.add('active'); }
    function hideLoading()  { document.getElementById('loadingOverlay').classList.remove('active'); }

    // ── Security ───────────────────────────────────────────────────────────────
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if(e.keyCode===123||(e.ctrlKey&&e.shiftKey&&[73,74].includes(e.keyCode))||(e.ctrlKey&&e.keyCode===85)) { e.preventDefault(); return false; }
        if(e.ctrlKey&&e.keyCode===80) { e.preventDefault(); return false; }
    });

    // Tab switch: 2 warnings max → 3rd = auto-submit
    document.addEventListener('visibilitychange', function() {
        if (document.hidden && !isAutoSubmitting) {
            tabSwitchCount++;
            logSecurityViolation('tab_switch', tabSwitchCount);
            logTabSwitch(); // save to test_tab_switches table

            if (tabSwitchCount > MAX_TAB_SWITCHES) {
                isAutoSubmitting = true;
                // 3rd switch → auto-submit as 'automatic'
                showWarnModal(
                    'Test Auto-Submitted!',
                    'You switched tabs too many times. The test is being submitted automatically.',
                    'danger'
                );
               setTimeout(() => autoSubmitTest(), 5000);
            } else {
                const left = MAX_TAB_SWITCHES - tabSwitchCount + 1;
                showWarnModal(
                    'Tab Switch Detected!',
                    `Switching tabs is not allowed during the test. If you switch tabs again, the test will be automatically submitted.`,
                    'warn'
                );
            }
        } else if (!document.hidden) {
            syncWithServer();
        }
    });

    document.addEventListener('copy',        e => e.preventDefault());
    document.addEventListener('paste',       e => e.preventDefault());
    document.addEventListener('cut',         e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());

    function logSecurityViolation(type, data) {
        fetch("{{ route('supervisor.test.log-violation') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID,violation_type:type,violation_data:data,timestamp:new Date().toISOString()})
        }).catch(console.error);
    }

    function logTabSwitch() {
        if (isAutoSubmitting) return; // Prevent extra logs during auto-submit delay
        fetch("{{ route('supervisor.test.log-tab-switch') }}", {
            method:'POST', headers:{'X-CSRF-TOKEN':CSRF_TOKEN,'Content-Type':'application/json'},
            body:JSON.stringify({attempt_id:ATTEMPT_ID, switched_at: new Date().toISOString()})
        }).catch(console.error);
    }
</script>

@endsection