@extends('layout.supervisor.app')

@section('content')


<div class="content">
    <div class="container-fluid">

       
        <div class="welcome-box">
            <div class="div-top-headings">
            <i class="fas fa-file-alt"></i><h1 class="page-title">Available Tests</h1></div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Global banner — shown once, only when ALL tests are blocked ── --}}
        @php
            $firstStatus = $tests->first()?->supervisor_status ?? null;
            $globalState = $firstStatus['state'] ?? null;
            // Only show banner for states that lock ALL tests
            $showGlobalBanner = in_array($globalState, ['under_review', 'wait_period', 'globally_locked']);
        @endphp

        @if($showGlobalBanner)
            @if($globalState === 'under_review')
                <div class="global-block-banner gbb-review">
                    <i class="fas fa-clock"></i>
                     <div>
                       <h4>All tests are temporarily locked.</h4>
                      <p>  Your attempt on <strong>{{ $firstStatus['blocking_test'] }}</strong> is under admin review.
                        Tests will unlock once the admin publishes the result.</p>
                    </div>
                </div>
            @elseif(in_array($globalState, ['wait_period', 'globally_locked']) && ($firstStatus['cooldown_ends'] ?? null))
                <div class="global-block-banner gbb-wait">
                    <i class="fas fa-hourglass-half"></i>
                    <div>
                        <strong>All tests are temporarily locked.</strong><br>
                        You recently attempted <strong>{{ $firstStatus['blocking_test'] }}</strong>.
                        You can attempt any test again after:
                        <strong>{{ $firstStatus['cooldown_ends']->format('d M Y, h:i A') }}</strong><br>
                        @include('components.cooldown-timer', [
                            'cooldown_ends' => $firstStatus['cooldown_ends'],
                            'id'            => 'avt_global_cd',
                        ])
                    </div>
                </div>
            @endif
        @endif

        @if($tests->isEmpty())
            <div class="avt-empty">
                <i class="fas fa-clipboard-list"></i>
                <h4 style="color:#0E253A;">No Tests Available</h4>
                <p>There are no active tests at the moment. Please check back later.</p>
            </div>
        @else
            @foreach($tests as $test)
                @php
                    $status = $test->supervisor_status;
                    $state  = $status['state'];

                    // Badge per state — only the ACTUAL state of THIS test
                    [$bc, $bt] = match($state) {
                        'not_paid'            => ['sp-pay',      'Pay to Attempt'],
                        'can_attempt'         => ['sp-ready',    'Ready to Attempt'],
                        'wait_period'         => ['sp-wait',     'Wait Period'],        // this test caused the block
                        'under_review'        => ['sp-review',   'Under Review'],       // this test is under review
                        'globally_locked'     => ['sp-locked',   'Locked'],             // another test caused the block
                        'pending_in_progress' => ['sp-progress', 'In Progress'],
                        default               => ['sp-pay',      'Pay to Attempt'],
                    };
                @endphp

                <div class="avt-card">
                    <div class="avt-card-header" onclick="toggleAvt({{ $test->id }})">
                        <div class="avt-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="avt-title">
                            <h4>{{ $test->name }}</h4>
                            <div class="avt-meta">
                                <span><i class="fas fa-clock"></i> {{ $test->timing }} Min</span>
                                <span><i class="fas fa-question-circle"></i> {{ $test->total_question }} Questions</span>
                                <span><i class="fas fa-star"></i> {{ $test->total_marks }} Total Marks</span>

                                 <span><i class="fas fa-star"></i> {{ $test->passing_marks }} Passing Marks</span>
                            </div>
                        </div>
                        <div class="avt-header-right">
                            <span class="status-pill {{ $bc }}">{{ $bt }}</span>
                            <i class="fas fa-chevron-down avt-chevron" id="chev-{{ $test->id }}"></i>
                        </div>
                    </div>

                    <div class="avt-body" id="avt-body-{{ $test->id }}">

                        <div class="avt-stats-row">
                            <div class="avt-stat"><div class="sv">{{ $test->timing }}</div><div class="sl">Minutes</div></div>
                            <div class="avt-stat"><div class="sv">{{ $test->total_question }}</div><div class="sl">Questions</div></div>
                            <div class="avt-stat"><div class="sv">{{ $test->total_marks }}</div><div class="sl">Total Marks</div></div>
                            <div class="avt-stat"><div class="sv">{{ $test->passing_marks }}</div><div class="sl">Passing Marks</div></div>
                        </div>

                         <div class="avt-pills">
                            <strong >Marks vary by question type:</strong>
                            @if($test->text_q_per_dept > 0)
                                <span class="avt-pill"><i class="fas fa-font"></i> <b>Text:</b> ({{ $test->text_marks }} mark each)</span>
                            @endif
                            @if($test->single_select_q_per_dept > 0)
                                <span class="avt-pill"><i class="fas fa-dot-circle"></i> <b>Single:</b> ({{ $test->single_select_marks }} mark each)</span>
                            @endif
                            @if($test->multi_select_q_per_dept > 0)
                                <span class="avt-pill"><i class="fas fa-check-square"></i> <b>Multi:</b> ({{ $test->multi_select_marks }} mark each)</span>
                            @endif
                            @if($test->image_q_per_dept > 0)
                                <span class="avt-pill"><i class="fas fa-image"></i> <b>Image:</b> ({{ $test->image_marks }} mark each)</span>
                            @endif
                            @if($test->video_q_per_dept > 0)
                                <span class="avt-pill"><i class="fas fa-video"></i> <b>Video:</b> ({{ $test->video_marks }} mark each)</span>
                            @endif
                        </div>

                        <div class="avt-fee-bar">
                            <div>
                                <p class="avt-fee-num">₹ {{ number_format($test->fees, 0) }}</p>
                                <p class="avt-fee-sub">Test fee (one-time per attempt)</p>
                            </div>

                            @if($state === 'can_attempt')
                                <a href="{{ route('supervisor.tests.attempt', $test->id) }}" class="btn-avt">
                                    <i class="fas fa-play-circle"></i> Attempt Now
                                </a>
                            @elseif($state === 'pending_in_progress')
                                <a href="{{ route('supervisor.tests.attempt', $test->id) }}" class="btn-avt">
                                    <i class="fas fa-redo"></i> Resume Test
                                </a>
                            @elseif($state === 'not_paid')
                                <a href="{{ route('supervisor.tests.show', $test->id) }}" class="btn-avt">
                                    <i class="fas fa-credit-card"></i> Pay & Attempt
                                </a>
                            @else
                                <span class="btn-avt-locked">
                                    <i class="fas fa-lock"></i>
                                    @if($state === 'under_review') Under Review
                                    @elseif($state === 'wait_period') Wait Period
                                    @else Locked
                                    @endif
                                </span>
                            @endif
                        </div>

                        {{-- Per-test inline notice (only show when NOT covered by global banner) --}}
                        @if($state === 'under_review')
                            <div class="avt-notice n-info">
                               <div class="notice-bls">
                                  <i class="fas fa-clock"></i>
                               <p> This test is currently under admin review. Results will be updated shortly.</p>
                            </div>
                            </div>
                        @elseif($state === 'wait_period' && ($status['cooldown_ends'] ?? null))
                            <div class="avt-notice n-warn">
                                <div class="notice-bls">
                                  <i class="fas fa-hourglass-half"></i>
                                <p>
                                    Wait period active. You can reattempt after:
                                    <strong>{{ $status['cooldown_ends']->format('d M Y, h:i A') }}</strong>
                                </p>
                              </div>
                            </div>
                        @elseif($state === 'globally_locked')
                            <div class="avt-notice n-lock">
                               <div class="notice-bls">
                                <i class="fas fa-lock" style="color: #f7a200;"></i>
                                <p>{{ $status['block_reason'] }}</p>
                            </div>
                            </div>
                        @endif

                        <a href="{{ route('supervisor.tests.show', $test->id) }}" class="avt-detail-link">
                            <i class="fas fa-external-link-alt"></i> View Details & Attempt History
                        </a>

                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>

<script>
function toggleAvt(id) {
    const body = document.getElementById('avt-body-' + id);
    const chev = document.getElementById('chev-' + id);
    const open = body.classList.contains('open');
    document.querySelectorAll('.avt-body').forEach(b => b.classList.remove('open'));
    document.querySelectorAll('.avt-chevron').forEach(c => c.classList.remove('open'));
    if (!open) { body.classList.add('open'); chev.classList.add('open'); }
}
</script>
@endsection