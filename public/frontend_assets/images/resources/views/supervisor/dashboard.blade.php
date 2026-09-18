@extends('layout.supervisor.app')

@section('content')


    <div class="content">
        <div class="container-fluid">

            {{-- Welcome --}}
            <div class="welcome-box">

                <div class="">
                    <div class="div-top-headings">
                        <i class="fas fa-user"></i>
                        <div>
                            <h1>Welcome, {{ ucfirst($supervisor->user->name) }}!</h1>
                            <p>Here's your qualification test status and overview.</p>
                        </div>
                    </div>
                </div>
                @if ($passedTest)
                    <div class="col-md-6">
                        <h2 style="text-align: end">Verified Supervisor!</h2>
                    </div>
                @endif

            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning">{{ session('warning') }}</div>
            @endif

            {{-- Status banner — single banner using updated state names --}}
            @if ($globalBlock && $globalBlock->block_state === 'under_review')
                <div class="dash-status-banner dsb-review">
                    <div class="under-reviews">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h4>Test Under Review</h4>
                            <p> Your attempt on <strong>{{ $globalBlock->test_name }}</strong> is under admin review.
                                All tests are locked until the result is published.</p>
                        </div>
                    </div>
                </div>
            @elseif($globalBlock && $globalBlock->block_state === 'cooldown')
                <div class="dash-status-banner dsb-wait">
                    <div class="galss-ads"> <i class="fas fa-hourglass-half"></i>
                        <div>
                            <h4>Wait Period Active</h4>
                            You recently attempted <strong>{{ $globalBlock->test_name }}</strong>.
                            Next attempt available at:
                            <strong>{{ $globalBlock->can_reattempt_after->format('d M Y, h:i A') }}</strong>
                            <br>

                        </div>
                    </div>
                    <div>
                        @include('components.cooldown-timer', [
                            'cooldown_ends' => $globalBlock->can_reattempt_after,
                            'id' => 'dash_cd',
                        ])
                    </div>
                </div>
            @elseif($passedTest)
                <div class="dash-status-banner dsb-pass">
                    <div class="congrats-bdsb">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h5>Congratulations {{ ucfirst($supervisor->user->name) }}!</h5>
                            <p>You passed <strong>{{ $passedTest->test_name }}</strong> with
                                <strong>{{ $passedTest->percentage }}%</strong>.
                                You can now apply for available jobs.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="dash-status-banner dsb-default">
                    <div class="">
                        <p class="test-qualify">Qualification Test</p>
                        <p class="test-heads"> Complete a test to get started
                        </p>
                        <p class="paragraphs">Pass at least one test to become a verified supervisor and apply for jobs.</p>
                    </div>
                    <a href="{{ route('supervisor.tests.index') }}" class="btn-avaible">View Available Tests →</a>
                </div>
            @endif

            {{-- Stat cards --}}
            <div class="dash-stats-row">
                <div class="dash-stat">
                    <div class="test-attem-all">
                        <div class="ds-icon ds-icon-blue"><i class="fas fa-clipboard-list"></i></div>
                        <div class="ds-label">Total Attempts</div>
                    </div>
                    <div class="ds-val">{{ $totalAttempts }}</div>
                </div>
                <div class="dash-stat">
                    <div class="test-attem-all">
                        <div class="ds-icon ds-icon-green"><i class="fas fa-check-circle"></i></div>
                        <div class="ds-label">Tests Passed</div>
                    </div>
                    <div class="ds-val">{{ $passedCount }}</div>
                </div>
                <div class="dash-stat">
                    <div class="test-attem-all">
                        <div class="ds-icon ds-icon-warn"><i class="fas fa-times-circle"></i></div>
                        <div class="ds-label">Tests Failed</div>
                    </div>

                    <div class="ds-val">{{ $failedCount }}</div>
                </div>
                <div class="dash-stat">
                    <div class="test-attem-all">
                        <div class="ds-icon ds-icon-dark"><i class="fas fa-file-alt"></i></div>
                        <div class="ds-label">Available Tests</div>
                    </div>
                    <div class="ds-val">{{ $availableTestsCount }}</div>
                </div>
            </div>

            {{-- Available Tests list --}}
            <div class="dash-box">
                <div class="dash-box-head">
                    <h3><i class="fas fa-list-alt"></i> Available Tests</h3>
                    <a href="{{ route('supervisor.tests.index') }}">View All</a>
                </div>
                <div>
                    @forelse($tests as $test)
                        @php
                            $ts = $test->supervisor_status;
                            $state = $ts['state'];
                            [$bc, $bt] = match ($state) {
                                'not_paid' => ['sp-pay', 'Pay to Attempt'],
                                'can_attempt' => ['sp-ready', 'Ready'],
                                'wait_period' => ['sp-wait', 'Wait Period'],
                                'under_review' => ['sp-review', 'Under Review'],
                                'globally_locked' => ['sp-locked', 'Locked'],
                                'pending_in_progress' => ['sp-progress', 'In Progress'],
                                default => ['sp-pay', 'Pay to Attempt'],
                            };
                            $lastForTest = $recentAttemptsByTest[$test->id] ?? null;
                        @endphp
                        <div class="test-list-item">
                            <div class="tli-icon"><i class="fas fa-file-alt"></i></div>
                            <div class="tli-info">
                                <p class="tli-name">{{ $test->name }}</p>
                                <div class="tli-meta">
                                    <span><i class="fas fa-clock"></i> {{ $test->timing }} Min</span>
                                    <span><i class="fas fa-question-circle"></i> {{ $test->total_question }} Questions</span>
                                    <span><i class="fas fa-star"></i> {{ $test->total_marks }} Total Marks</span>
                                    <span class="test-pass">Passing Marks: {{ $test->passing_marks }}</span>

                                    @if ($lastForTest)
                                        @if ($lastForTest->score_visible)
                                            <span
                                                style="color:{{ $lastForTest->score_obtained >= $lastForTest->passing_marks ? '#155724' : '#721c24' }};font-weight:600;">
                                                Last Attepmt Result: {{ $lastForTest->score_obtained }}/{{ $lastForTest->total_marks }}
                                                ({{ $lastForTest->percentage }}%)
                                            </span>
                                        @else
                                            <span style="color:#856404;font-weight:600;">Last Attepmt Result: Pending Review</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                            <div class="tli-right">
                                <span class="status-pill {{ $bc }}">{{ $bt }}</span>
                                <a href="{{ route('supervisor.tests.show', $test->id) }}" class="btn-dash-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </div>
                        </div>
                    @empty
                        <div style="text-align:center;padding:30px;color:#5a7284;font-size:14px;">
                            <i class="fas fa-clipboard-list"
                                style="font-size:28px;opacity:.2;display:block;margin-bottom:8px;"></i>
                            No active tests available at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Recent History --}}
            <div class="dash-box">
                <div class="dash-box-head">
                    <h3><i class="fas fa-history"></i> Recent Attempt History</h3>
                    <a href="{{ route('supervisor.tests.index') }}">View All Tests</a>
                </div>
                @if ($recentAttempts->count() > 0)
                    <div style="overflow-x:auto;">
                        <table class="hist-table">
                            <thead>
                                <tr>
                                    <th>Test Name</th>
                                    <th>Score</th>
                                    <th>Percentage</th>
                                    <th>Submission</th>
                                    <th>Time Taken</th>
                                    <th>Admin Suggested Skills by Admin</th>
                                    <th>Rejection Reason</th>
                                    <th>Result</th>
                                    <th>Submitted At</th>
                                    <th>Reattempt From</th>
                                    <th>Dept. Wise Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAttempts->take(10) as $attempt)
                                    @php
                                        $pct = (float) $attempt->percentage;
                                        $isPassing = $attempt->score_obtained >= $attempt->passing_marks;
                                        [$hbc, $hbl] = match ($attempt->status) {
                                            'pass' => ['hb-pass', 'Passed'],
                                            'fail' => ['hb-fail', 'Failed'],
                                            'underReview' => ['hb-review', 'Under Review'],
                                            default => ['hb-review', ucfirst($attempt->status)],
                                        };
                                    @endphp
                                    <tr>
                                        <td style="font-weight:600;font-size:15px;color:#00263a;">{{ $attempt->test_name }}
                                        </td>

                                        <td>
                                            @if ($attempt->score_visible)
                                                <strong>{{ $attempt->score_obtained }}</strong>
                                                <span> / {{ $attempt->total_marks }}</span>
                                            @else
                                                <span class="score-pending"><i class="fas fa-clock"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($attempt->score_visible)
                                                <span
                                                    style="font-weight:700;color:{{ $isPassing ? '#155724' : '#721c24' }};font-size:12px">{{ $pct }}%</span>

                                                <div class="pct-bar-bg" style="width:80px;">
                                                    <div class="pct-bar-fill {{ $isPassing ? 'pct-pass-fill' : 'pct-fail-fill' }}"
                                                        style="width:{{ min($pct, 100) }}%"></div>
                                                </div>
                                            @else
                                                <span style="color:#BEC8CD;">—</span>
                                            @endif
                                        </td>
                                        <td data-label="Submission">
                                            <span
                                                class="sub-badge {{ $attempt->test_submission_status === 'automatic' ? 'sb-auto' : 'sb-sup' }}">
                                                {{ ucfirst($attempt->test_submission_status) }}
                                            </span>
                                        </td>
                                        <td style="font-size:12px;font-weight:600;white-space:nowrap;">
                                            {{ $attempt->formatted_duration }}</td>



                                        <td style="font-size:13px;" class="skills-test-one">

                                            @if ($attempt->testSkills && $attempt->testSkills->count() > 0)
                                                @foreach ($attempt->testSkills as $ts)
                                                    <span class="sub-badge sb-sup2">{{ $ts->skill->name }}</span>
                                                @endforeach
                                            @else
                                                <span style="color:#BEC8CD;">—</span>
                                            @endif
                                        </td>
                                        <td style="font-size:13px;">
                                            @if ($attempt->status === 'fail' && $attempt->reject_reason)
                                                <span style="color:#dc3545;">{{ $attempt->reject_reason }}</span>
                                            @else
                                                <span style="color:#BEC8CD;">—</span>
                                            @endif
                                        </td>
                                        <td><span class="hbadge {{ $hbc }}">{{ $hbl }}</span></td>

                                        <td style="font-weight: 600;">
                                            {{ $attempt->submitted_at?->format('d M Y') }}<br>
                                            <span>{{ $attempt->submitted_at?->format('h:i A') }}</span>
                                        </td>
                                        <td style="font-weight: 600;">
                                            @if ($attempt->can_reattempt_after)
                                                {{ \Carbon\Carbon::parse($attempt->can_reattempt_after)->format('d M Y') }}<br>
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
                        <i class="fas fa-history"></i>
                        <h4> No test attempts yet.</h4>
                        <p>
                            Give a test to see your history here.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
