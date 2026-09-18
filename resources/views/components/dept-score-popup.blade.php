{{--
    ╔══════════════════════════════════════════════════════════════╗
    ║  Department-Wise Score Popup — Global Reusable Component     ║
    ║                                                              ║
    ║  USAGE:                                                      ║
    ║  1. Include once in your layout file:                        ║
    ║     @include('components.dept-score-popup')                  ║
    ║                                                              ║
    ║  2. Trigger with a button:                                   ║
    ║     onclick="DeptScorePopup.open('{{ route('test.dept-score', $attempt->id) }}')"
    ╚══════════════════════════════════════════════════════════════╝
--}}

{{-- ═══════════════════ POPUP HTML STRUCTURE ═══════════════════ --}}
<div id="deptScoreOverlay" class="dsp-overlay" onclick="DeptScorePopup._onOverlayClick(event)">
    <div class="dsp-modal" role="dialog" aria-labelledby="dspTitle" aria-modal="true">

        {{-- Close Button --}}
        <button class="dsp-close-btn" onclick="DeptScorePopup.close()" aria-label="Close">
            <i class="fas fa-times"></i>
        </button>

        {{-- Spinner State --}}
        <div class="dsp-spinner" id="dspSpinner">
            <div class="dsp-spinner-ring"></div>
            <p class="dsp-spinner-text">Loading department scores…</p>
        </div>

        {{-- Content State --}}
        <div class="dsp-content" id="dspContent" style="display:none;">

            {{-- Header --}}
            <div class="dsp-header">
                <div class="dsp-header-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="dsp-header-text">
                    <p class="dsp-subtitle">Department-Wise Score Breakdown</p>
                    <h2 class="dsp-title" id="dspTitle">—</h2>
                </div>
            </div>

            {{-- Overall Score Summary --}}
            <div class="dsp-summary" id="dspSummary">
                <div class="dsp-sum-item">
                    <span class="dsp-sum-label">Overall Score</span>
                    <span class="dsp-sum-val" id="dspOverallScore">—</span>
                </div>
                <div class="dsp-sum-divider"></div>
                <div class="dsp-sum-item">
                    <span class="dsp-sum-label">Percentage</span>
                    <span class="dsp-sum-val" id="dspOverallPct">—</span>
                </div>
                <div class="dsp-sum-divider"></div>
                <div class="dsp-sum-item">
                    <span class="dsp-sum-label">Result</span>
                    <span class="dsp-sum-badge" id="dspOverallBadge">—</span>
                </div>
            </div>

            {{-- Department Cards --}}
            <div class="dsp-dept-grid" id="dspDeptGrid">
                {{-- Injected by JS --}}
            </div>

        </div>

        {{-- Error State --}}
        <div class="dsp-error" id="dspError" style="display:none;">
            <i class="fas fa-exclamation-circle dsp-err-icon"></i>
            <p class="dsp-err-msg" id="dspErrMsg">Failed to load scores. Please try again.</p>
            <button class="dsp-retry-btn" id="dspRetryBtn">
                <i class="fas fa-redo"></i> Retry
            </button>
        </div>

    </div>
</div>

{{-- ═══════════════════ STYLES ═══════════════════ --}}
<style>
    /* ── Overlay ────────────────────────────────────────────── */
    .dsp-overlay {
        position: fixed;
        inset: 0;
        background: rgba(10, 22, 40, 0.72);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.25s ease, visibility 0.25s ease;
    }

    .dsp-overlay.dsp-active {
        opacity: 1;
        visibility: visible;
    }

    /* ── Modal Card ─────────────────────────────────────────── */
    .dsp-modal {
        background: #ffffff;
        border-radius: 20px;
        width: 100%;
        max-width: 560px;
        max-height: 88vh;
        overflow-y: auto;
        position: relative;
        box-shadow:
            0 24px 60px rgba(0, 70, 109, 0.22),
            0 4px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(24px) scale(0.97);
        transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
        scrollbar-width: thin;
        scrollbar-color: #d0dce8 transparent;
    }

    .dsp-overlay.dsp-active .dsp-modal {
        transform: translateY(0) scale(1);
    }

    .dsp-modal::-webkit-scrollbar {
        width: 5px;
    }

    .dsp-modal::-webkit-scrollbar-track {
        background: transparent;
    }

    .dsp-modal::-webkit-scrollbar-thumb {
        background-color: #d0dce8;
        border-radius: 4px;
    }

    /* ── Close Button ───────────────────────────────────────── */
    .dsp-close-btn {
        position: absolute;
        top: 14px;
        right: 16px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #f0f4f8;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #5a7284;
        font-size: 14px;
        transition: background 0.2s, color 0.2s, transform 0.15s;
        z-index: 10;
    }

    .dsp-close-btn:hover {
        background: #e2eaf0;
        color: #0E253A;
        transform: rotate(90deg);
    }

    /* ── Spinner ────────────────────────────────────────────── */
    .dsp-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 32px;
        gap: 18px;
    }

    .dsp-spinner-ring {
        width: 52px;
        height: 52px;
        border: 4px solid #e0eaf2;
        border-top-color: #00466D;
        border-radius: 50%;
        animation: dsp-spin 0.85s linear infinite;
    }

    @keyframes dsp-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .dsp-spinner-text {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        color: #5a7284;
        margin: 0;
        letter-spacing: 0.02em;
    }

    /* ── Header ─────────────────────────────────────────────── */
    .dsp-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 28px 28px 0;
    }

    .dsp-header-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #00466D, #0E253A);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dsp-header-icon i {
        color: #ffffff;
        font-size: 20px;
    }

    .dsp-subtitle {
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        font-weight: 600;
        color: #00466D;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin: 0 0 3px;
    }

    .dsp-title {
        font-family: 'Poppins', sans-serif;
        font-size: 17px;
        font-weight: 700;
        color: #0E253A;
        margin: 0;
        line-height: 1.3;
    }

    /* ── Summary Strip ──────────────────────────────────────── */
    .dsp-summary {
        display: flex;
        align-items: center;
        gap: 0;
        background: linear-gradient(135deg, #f4f9fc 0%, #eef4f9 100%);
        border: 1.5px solid #d8eaf4;
        border-radius: 12px;
        margin: 18px 28px 0;
        overflow: hidden;
    }

    .dsp-sum-item {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 14px 10px;
        gap: 4px;
    }

    .dsp-sum-divider {
        width: 1.5px;
        height: 36px;
        background: #c8dcea;
        flex-shrink: 0;
    }

    .dsp-sum-label {
        font-family: 'Poppins', sans-serif;
        font-size: 10px;
        font-weight: 600;
        color: #5a7284;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    .dsp-sum-val {
        font-family: 'Poppins', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #0E253A;
        line-height: 1;
    }

    .dsp-sum-badge {
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
        font-weight: 700;
        padding: 3px 14px;
        border-radius: 20px;
        letter-spacing: 0.02em;
    }

    .dsp-badge-pass {
        background: #d4edda;
        color: #155724;
    }

    .dsp-badge-fail {
        background: #f8d7da;
        color: #721c24;
    }

    .dsp-badge-review {
        background: #cce5ff;
        color: #004085;
    }

    .dsp-badge-def {
        background: #e2e8f0;
        color: #374151;
    }

    /* ── Department Grid ─────────────────────────────────────── */
    .dsp-dept-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
        padding: 20px 28px 28px;
    }

    /* ── Department Card ─────────────────────────────────────── */
    .dsp-dept-card {
        background: #ffffff;
        border: 1.5px solid #e0e9f0;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 2px 8px rgba(14, 37, 58, 0.06);
        transition: box-shadow 0.2s, transform 0.2s;
        animation: dsp-fadeUp 0.35s ease both;
    }

    .dsp-dept-card:hover {
        box-shadow: 0 6px 20px rgba(0, 70, 109, 0.14);
        transform: translateY(-2px);
    }

    @keyframes dsp-fadeUp {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dsp-dept-name {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #0E253A;
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .dsp-dept-icon {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        background: linear-gradient(135deg, #00466D, #0E253A);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .dsp-dept-icon i {
        color: #fff;
        font-size: 11px;
    }

    /* Progress bar */
    .dsp-progress-wrap {
        position: relative;
        background: #e9eef4;
        border-radius: 8px;
        height: 8px;
        overflow: hidden;
        margin-bottom: 10px;
    }

    .dsp-progress-fill {
        height: 100%;
        border-radius: 8px;
        width: 0%;
        transition: width 0.9s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .dsp-fill-high {
        background: linear-gradient(90deg, #28a745, #34d058);
    }

    .dsp-fill-mid {
        background: linear-gradient(90deg, #fd7e14, #ffb347);
    }

    .dsp-fill-low {
        background: linear-gradient(90deg, #dc3545, #f06a75);
    }

    .dsp-dept-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dsp-dept-marks {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
        color: #0E253A;
    }

    .dsp-dept-marks span {
        font-size: 11px;
        font-weight: 500;
        color: #5a7284;
    }

    .dsp-dept-pct {
        font-family: 'Poppins', sans-serif;
        font-size: 13px;
        font-weight: 700;
        min-width: 42px;
        text-align: right;
    }

    .dsp-pct-high {
        color: #155724;
    }

    .dsp-pct-mid {
        color: #7b4400;
    }

    .dsp-pct-low {
        color: #721c24;
    }

    .dsp-dept-qs {
        margin-top: 8px;
        font-family: 'Poppins', sans-serif;
        font-size: 11px;
        color: #5a7284;
    }

    .dsp-dept-qs strong {
        color: #0E253A;
    }

    /* ── Error State ─────────────────────────────────────────── */
    .dsp-error {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 32px;
        gap: 14px;
        text-align: center;
    }

    .dsp-err-icon {
        font-size: 38px;
        color: #dc3545;
        opacity: 0.7;
    }

    .dsp-err-msg {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        color: #5a7284;
        margin: 0;
    }

    .dsp-retry-btn {
        background: #00466D;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .dsp-retry-btn:hover {
        background: #003459;
    }

    /* ── "View Dept. Wise Score" trigger button ───────────────────── */
    .btn-dept-score {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: linear-gradient(135deg, #00466D, #0a5c8a);
        color: #ffffff;
        border: none;
        border-radius: 7px;
        padding: 5px 11px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        white-space: nowrap;
        transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
        text-decoration: none;
        vertical-align: middle;
        box-shadow: 0 2px 6px rgba(0, 70, 109, 0.25);
    }

    .btn-dept-score:hover {
        background: linear-gradient(135deg, #003459, #00466D);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 70, 109, 0.35);
        color: #fff;
    }

    .btn-dept-score i {
        font-size: 10px;
    }

    /* ── Pending score state (no button shown) ───────────────── */
    .btn-dept-score-pending {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffc107;
        border-radius: 7px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        cursor: default;
        white-space: nowrap;
    }

    /* ── Responsive ──────────────────────────────────────────── */
    @media (max-width: 480px) {
        .dsp-modal {
            border-radius: 16px;
        }

        .dsp-header {
            padding: 22px 18px 0;
        }

        .dsp-summary {
            margin: 14px 18px 0;
        }

        .dsp-dept-grid {
            padding: 16px 18px 22px;
            grid-template-columns: 1fr;
        }

        .dsp-sum-val {
            font-size: 15px;
        }
    }
</style>

{{-- ═══════════════════ JAVASCRIPT ═══════════════════ --}}
<script>
    window.DeptScorePopup = (function() {

        var _overlay = null;
        var _spinner = null;
        var _content = null;
        var _error = null;
        var _currentUrl = null;

        function _init() {
            _overlay = document.getElementById('deptScoreOverlay');
            _spinner = document.getElementById('dspSpinner');
            _content = document.getElementById('dspContent');
            _error = document.getElementById('dspError');

            document.getElementById('dspRetryBtn').addEventListener('click', function() {
                if (_currentUrl) _fetch(_currentUrl);
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') close();
            });
        }

        function open(url) {
            _currentUrl = url;
            _showState('spinner');
            _overlay.classList.add('dsp-active');
            document.body.style.overflow = 'hidden';
            _fetch(url);
        }

        function close() {
            _overlay.classList.remove('dsp-active');
            document.body.style.overflow = '';
            // Small delay then reset state
            setTimeout(function() {
                _showState('spinner');
                _currentUrl = null;
            }, 280);
        }

        function _onOverlayClick(e) {
            if (e.target === _overlay) close();
        }

        function _showState(state) {
            _spinner.style.display = state === 'spinner' ? 'flex' : 'none';
            _content.style.display = state === 'content' ? 'block' : 'none';
            _error.style.display = state === 'error' ? 'flex' : 'none';
        }

        function _fetch(url) {
            _showState('spinner');

            // Get CSRF token (Laravel meta or cookie)
            var csrf = (function() {
                var m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : '';
            })();

            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            if (csrf) xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            _render(data);
                            _showState('content');
                        } else {
                            _showError(data.message || 'Unexpected error.');
                        }
                    } catch (ex) {
                        _showError('Invalid response from server.');
                    }
                } else {
                    _showError('Server error (' + xhr.status + '). Please try again.');
                }
            };

            xhr.onerror = function() {
                _showError('Network error. Please check your connection.');
            };

            xhr.send();
        }

        function _render(data) {
            // Title & subtitle
            document.getElementById('dspTitle').textContent = data.test_name || '—';

            // Summary
            document.getElementById('dspOverallScore').textContent =
                data.score + ' / ' + data.total;
            document.getElementById('dspOverallPct').textContent =
                (data.percentage || 0) + '%';

            var badge = document.getElementById('dspOverallBadge');
            var status = data.status || '';
            badge.textContent = _statusLabel(status);
            badge.className = 'dsp-sum-badge ' + _statusBadgeClass(status);

            // Department cards
            var grid = document.getElementById('dspDeptGrid');
            grid.innerHTML = '';

            if (!data.departments || data.departments.length === 0) {
                grid.innerHTML =
                    '<p style="grid-column:1/-1;text-align:center;color:#5a7284;font-family:Poppins,sans-serif;font-size:13px;padding:20px 0;">No department data available.</p>';
                return;
            }

            data.departments.forEach(function(dept, idx) {
                var card = document.createElement('div');
                card.className = 'dsp-dept-card';
                card.style.animationDelay = (idx * 0.06) + 's';

                var pct = dept.percentage || 0;
                var fillClass = pct >= 60 ? 'dsp-fill-high' : (pct >= 35 ? 'dsp-fill-mid' :
                    'dsp-fill-low');
                var pctClass = pct >= 60 ? 'dsp-pct-high' : (pct >= 35 ? 'dsp-pct-mid' : 'dsp-pct-low');

                card.innerHTML =
                    '<p class="dsp-dept-name">' +
                    '<span class="dsp-dept-icon"><i class="fas fa-layer-group"></i></span>' +
                    _escHtml(dept.department) +
                    '</p>' +
                    '<div class="dsp-progress-wrap">' +
                    '<div class="dsp-progress-fill ' + fillClass + '" data-width="' + Math.min(pct,
                    100) + '"></div>' +
                    '</div>' +
                    '<div class="dsp-dept-meta">' +
                    '<div class="dsp-dept-marks">' +
                    dept.marks_earned + '<span> / ' + dept.total_marks + ' marks</span>' +
                    '</div>' +
                    '<div class="dsp-dept-pct ' + pctClass + '">' + pct + '%</div>' +
                    '</div>' +
                    '<div class="dsp-dept-qs">' +
                    '<strong>' + dept.correct_q + '</strong> of <strong>' + dept.total_q +
                    '</strong> questions correct' +
                    '</div>';

                grid.appendChild(card);
            });

            // Animate progress bars after DOM insertion
            requestAnimationFrame(function() {
                requestAnimationFrame(function() {
                    grid.querySelectorAll('.dsp-progress-fill').forEach(function(bar) {
                        bar.style.width = bar.getAttribute('data-width') + '%';
                    });
                });
            });
        }

        function _showError(msg) {
            document.getElementById('dspErrMsg').textContent = msg;
            _showState('error');
        }

        function _statusLabel(status) {
            return {
                pass: 'Passed',
                fail: 'Failed',
                underReview: 'Under Review'
            } [status] ||
            (status ? status.charAt(0).toUpperCase() + status.slice(1) : '—');
        }

        function _statusBadgeClass(status) {
            return {
                pass: 'dsp-badge-pass',
                fail: 'dsp-badge-fail',
                underReview: 'dsp-badge-review'
            } [status] ||
            'dsp-badge-def';
        }

        function _escHtml(str) {
            var d = document.createElement('div');
            d.appendChild(document.createTextNode(str || ''));
            return d.innerHTML;
        }

        // Init when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', _init);
        } else {
            _init();
        }

        // Public API
        return {
            open: open,
            close: close,
            _onOverlayClick: _onOverlayClick,
        };
    })();
</script>
