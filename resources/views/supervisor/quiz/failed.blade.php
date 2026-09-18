@extends('layout.supervisor.app')

@section('content')
    @php
        use Carbon\Carbon;
        $canReattemptAfter = Carbon::parse($canReattemptAfter);
    @endphp
    <div class="content">
        <div class="container-fluid">
            <div class="row justify-content-center align-items-center">
                <div class="col-12">
                    <div class="failed-container">
                        <div class="failed-icon">
                            <div class="outer-circle">
                                <div class="inner-circle">
                                    <i class="fas fa-times cross-mark"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="failed-title">Unfortunately, you did not clear the<br>qualification test this time.
                        </h1>
                        <div class="title-underline2"></div>
                        <div class="message-box2">
                            <p>You may <span class="highlight2">reattempt the test after 24 hours</span>. Please revisit
                                your dashboard
                                to attempt the test again. We encourage you to prepare and try once more — wishing you
                                the very best for
                                your next attempt.</p>
                        </div>

                       @if (isset($minutesRemaining) && $minutesRemaining > 0)
    <div class="alert alert-warning mt-4">
        <strong>Next attempt details:</strong><br>
      
        <small>
            <strong> Available From: {{ $canReattemptAfter->format('d M Y, h:i A') }}</strong>
        </small>
         
    </div>

    <div id="countdown" class="mt-3">
        <h4>Time Remaining:</h4>
        <div style="font-size: 24px; font-weight: bold;" id="countdownDisplay">
            Calculating...
        </div>
    </div>
@endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (isset($canReattemptAfter))
        <script>
            const endTime = new Date("{{ $canReattemptAfter->toIso8601String() }}").getTime();



            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    document.getElementById('countdownDisplay').innerHTML = "Reattempt Available Now!";
                    clearInterval(countdownInterval);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    return;
                }

                const hours = Math.floor(distance / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                document.getElementById('countdownDisplay').innerHTML =
                    `${hours}h ${minutes}m ${seconds}s`;
            }

            updateCountdown();
            const countdownInterval = setInterval(updateCountdown, 1000);
        </script>
    @endif
@endsection
