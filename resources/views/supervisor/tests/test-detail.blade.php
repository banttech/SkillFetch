@extends('layout.supervisor.app')

@section('content')
<style>
    @media(max-width:768px){.dsp-sum-item {
    align-items: flex-start!important;
    justify-content: flex-start!important;
}}
</style>

    <div class="content">
        <div class="container-fluid">

            <div class="text-right">
                <a href="{{ route('supervisor.tests.index') }}" class="td-back">
                    <i class="fas fa-arrow-left"></i> Back to Available Tests
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Hero --}}
            <div class="td-hero">
                <h2>{{ $test->name }}</h2>
                <p>Qualification Test</p>
                <div class="td-hero-stats">

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-clock"></i>
                            <span class="l">Minutes</span></span>
                        <span class="v">{{ $test->timing }}</span>
                    </div>

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-question-circle"></i>
                            <span class="l">Questions</span></span>
                        <span class="v">{{ $test->total_question }}</span>
                    </div>

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-star"></i>
                            <span class="l">Total Marks</span></span>
                        <span class="v">{{ $test->total_marks }}</span>
                    </div>

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-check-circle"></i>
                            <span class="l">Passing Marks</span></span>
                        <span class="v">{{ $test->passing_marks }}</span>
                    </div>

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-building"></i>
                            <span class="l">Departments</span></span>
                        <span class="v">{{ $test->no_of_departments }}</span>
                    </div>

                    <div class="td-hs">
                        <span class="coomon-test-clock"> <i class="fas fa-layer-group"></i>
                            <span class="l">Questions/Dept</span></span>
                        <span class="v">{{ $test->question_per_department }}</span>
                    </div>

                </div>
            </div>
            {{-- Pay / Action bar --}}
            <div class="td-pay-bar">
                <div>
                    <p class="td-pay-amt">₹ {{ number_format($test->fees, 0) }}</p>
                    <p class="td-pay-sub">Test fee per attempt (non-refundable)</p>
                </div>

                @if ($status['state'] === 'can_attempt')
                    <a href="{{ route('supervisor.tests.attempt', $test->id) }}" class="btn-td">
                        <i class="fas fa-play-circle"></i> Start Test Now
                    </a>
                @elseif($status['state'] === 'pending_in_progress')
                    <a href="{{ route('supervisor.tests.attempt', $test->id) }}" class="btn-td">
                        <i class="fas fa-redo"></i> Resume Test
                    </a>
                @elseif($status['state'] === 'not_paid')
                    @include('components.razorpay-payment-button', [
                        'buttonId' => 'payBtn_' . $test->id,
                        'buttonText' => 'Pay ₹' . number_format($test->fees, 0) . ' & Attempt',
                        'initiateRoute' => route('supervisor.payment.initiate'),
                        'redirectUrl' => route('supervisor.tests.show', $test->id),
                        'paymentName' => $test->name . ' Test Fee',
                        'requestBody' => ['test_id' => $test->id],
                        'checkCamera' => true,
                    ])
                @else
                    {{-- Locked for any blocking state --}}
                    <span class="btn-td-locked">
                        <i class="fas fa-lock"></i>
                        @if ($status['state'] === 'under_review')
                            Under Review
                        @elseif($status['state'] === 'wait_period')
                            Wait Period
                        @else
                            Locked
                        @endif
                    </span>
                @endif
            </div>

            {{-- Status notices — using new state names --}}
            @if ($status['state'] === 'under_review')
                <div class="td-notice n-info">
                    <div class="test-box-bs">
                        <i class="fas fa-clock"></i>
                        <p>This test is currently under admin review. Your score and result will be updated once the admin
                            completes the review.</p>
                    </div>
                </div>
            @elseif($status['state'] === 'wait_period' && ($status['cooldown_ends'] ?? null))
                <div class="td-notice n-warn">
                    <div class="galss-ads">
                        <i class="fas fa-hourglass-half"></i>
                        <div>
                            Wait period active. You can reattempt this test after:
                            <strong>{{ $status['cooldown_ends']->format('d M Y, h:i A') }}</strong>

                        </div>
                    </div>
                    <div>
                        @include('components.cooldown-timer', [
                            'cooldown_ends' => $status['cooldown_ends'],
                            'id' => 'td_wait_cd',
                        ])
                    </div>
                </div>
            @elseif($status['state'] === 'globally_locked')
                <div class="td-notice n-lock">
                    <div class="test-n-locks">
                        <i class="fas fa-lock"></i>
                        <p>
                            {{ $status['block_reason'] }}
                            @if ($status['cooldown_ends'] ?? null)
                                <br>Unlocks at: <strong>{{ $status['cooldown_ends']->format('d M Y, h:i A') }}</strong><br>
                                @include('components.cooldown-timer', [
                                    'cooldown_ends' => $status['cooldown_ends'],
                                    'id' => 'td_global_cd',
                                ])
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            {{-- Terms & Conditions --}}
            <div class="td-box">
                <div class="td-box-head">
                    <i class="fas fa-shield-alt"></i>
                    <h3>Terms & Conditions</h3>
                </div>
                <ul class="tnc-list">
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Complete within <strong>{{ $test->timing }}
                                minutes</strong>. Timer starts immediately when you begin.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Once started, the test cannot be paused.
                            Ensure a stable internet connection before starting.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Switching tabs more than 2 times will trigger
                            automatic submission.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>You need <strong>{{ $test->passing_marks }}
                                marks out of {{ $test->total_marks }}</strong> to pass.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Each attempt requires a separate payment of
                            <strong>₹{{ number_format($test->fees, 0) }}</strong>.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Reattempt allowed after the wait period ends
                            — applicable for both pass and fail.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Text-type answers are manually reviewed by
                            admin. Final score updates after admin review.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Answers are auto-saved. Do not refresh or
                            close the browser during the test.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>Copy/paste, right-click and printing are
                            disabled during the test.</span></li>
                    <li><span><i class="fas fa-check-circle"></i></span> <span>The test fee is non-refundable once payment
                            is confirmed.</span></li>
                </ul>
            </div>

            {{-- Attempt History --}}
            <div class="td-box">
                <div class="td-box-head">
                    <i class="fas fa-history"></i>
                    <h3>
                        Attempt History
                        @if ($attempts->count() > 0)
                            <span class="count-badge">{{ $attempts->count() }}
                                attempt{{ $attempts->count() > 1 ? 's' : '' }}</span>
                        @endif
                    </h3>
                </div>

                @if ($attempts->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="hist-table">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    {{-- <th>Passing Marks</th> --}}
                                    <th>Submission</th>
                                    <th>Time Taken</th>
                                    <th>Suggested Skills by Admin</th>
                                    <th>Rejection Reason</th>
                                    <th>Result</th>
                                    <th>Submitted At</th>
                                    <th>Reattempt From</th>
                                    <th>Dept. Wise Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attempts as $i => $attempt)
                                    @php
                                        $attemptNo = $attempts->count() - $i;
                                        $pct = (float) $attempt->percentage;
                                        $isPassing = $attempt->score_obtained >= $attempt->passing_marks;
                                        $scoreVisible = $attempt->score_visible;

                                        [$hbc, $hbl] = match ($attempt->status) {
                                            'pass' => ['hb-pass', 'Passed'],
                                            'fail' => ['hb-fail', 'Failed'],
                                            'underReview' => ['hb-review', 'Under Review'],
                                            default => ['hb-other', ucfirst($attempt->status)],
                                        };
                                    @endphp
                                    <tr>
                                        <td data-label="Attempt"
                                            style="font-size:15px;font-weight: 600;
    color: #00263a;">
                                            {{ $attempt->test_name }}</td>

                                        <td data-label="Score" style="color:#2f2f2f">
                                            @if ($scoreVisible)
                                                <span class="score-main">{{ $attempt->score_obtained }}</span>
                                                <span class="score-total"> / {{ $attempt->total_marks }}</span>
                                            @else
                                                <span class="score-pending"><i class="fas fa-clock"></i> Pending
                                                    Review</span>
                                            @endif
                                        </td>

                                        <td data-label="Percentage">
                                            @if ($scoreVisible)
                                                <div class="pct-wrap">
                                                    <span
                                                        class="pct-label {{ $isPassing ? 'pct-pass-label' : 'pct-fail-label' }}">{{ $pct }}%</span>
                                                    <div class="pct-bar-bg">
                                                        <div class="pct-bar-fill {{ $isPassing ? 'pct-pass-fill' : 'pct-fail-fill' }}"
                                                            style="width: {{ min($pct, 100) }}%"></div>
                                                    </div>
                                                </div>
                                            @else
                                                <span>—</span>
                                            @endif
                                        </td>

                                        {{-- <td data-label="Passing Marks">{{ $attempt->passing_marks }}</td> --}}

                                        <td data-label="Submission" class="skills-test-one">
                                            <span
                                                class="sub-badge {{ $attempt->test_submission_status === 'automatic' ? 'sb-auto' : 'sb-sup' }}">
                                                {{ ucfirst($attempt->test_submission_status) }}
                                            </span>
                                        </td>

                                        <td data-label="Duration">
                                            {{ $attempt->formatted_duration }}
                                        </td>



                                        <td data-label="Suggested Skills by Admin" class="skills-test-one">
                                            @if ($attempt->testSkills && $attempt->testSkills->count() > 0)
                                                @foreach ($attempt->testSkills as $ts)
                                                    <span class="sub-badge sb-sup2">{{ $ts->skill->name }}</span>
                                                @endforeach
                                            @else
                                                <span>—</span>
                                            @endif
                                        </td>

                                        <td data-label="Rejection Reason">
                                            @if ($attempt->status === 'fail' && $attempt->reject_reason)
                                                <span
                                                    style="    color: #dc3545;
    font-weight: 600;">{{ $attempt->reject_reason }}</span>
                                            @else
                                                <span>—</span>
                                            @endif
                                        </td>

                                        <td data-label="Result"><span
                                                class="hbadge {{ $hbc }}">{{ $hbl }}</span></td>
                                        <td data-label="Submitted At">
                                            <span style="font-weight: 600">
                                                {{ $attempt->submitted_at?->format('d M Y') }}</span><br>
                                            <span>{{ $attempt->submitted_at?->format('h:i A') }}</span>
                                        </td>
                                        <td data-label="Reattempt From">
                                            @if ($attempt->can_reattempt_after)
                                                <span
                                                    style="font-weight: 600">{{ \Carbon\Carbon::parse($attempt->can_reattempt_after)->format('d M Y') }}</span><br>
                                                <span>{{ \Carbon\Carbon::parse($attempt->can_reattempt_after)->format('h:i A') }}</span>
                                            @else
                                                <span>—</span>
                                            @endif
                                        </td>
 <td>
                                            @if ($attempt->score_visible)
                                                <button class="btn-dept-score"
                                                    onclick="DeptScorePopup.open('{{ route('test.dept-score', $attempt->id) }}')"
                                                    title="View Department-wise Score">
                                                    <i class="fas fa-chart-bar"></i> Dept. Wise Score
                                                </button>
                                            @else
                                                <span class="btn-dept-score-pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="test-no-attemp">
                        <i class="fas fa-clipboard-list"></i>
                        <h4>No attempts yet for this test.</h4>
                        <p>Your attempt history will appear here after you give the test.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection
