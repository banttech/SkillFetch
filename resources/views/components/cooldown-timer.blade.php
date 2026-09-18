{{--
    Global Cooldown Timer Component
    ================================
    Usage anywhere in any blade:

    @include('components.cooldown-timer', [
        'cooldown_ends' => $cooldownEndsCarbon,   // Carbon instance
        'label'         => 'Next attempt in',      // optional label
        'reload'        => true,                   // optional: reload page when done (default true)
        'id'            => 'unique_id',            // optional: unique ID if multiple on same page
    ])
--}}

@php
    $timerId  = $id        ?? 'cdTimer_' . uniqid();
    $doReload = $reload    ?? true;
    $label    = $label     ?? 'Next attempt available in';
    $isoTime  = $cooldown_ends instanceof \Carbon\Carbon
                    ? $cooldown_ends->toIso8601String()
                    : \Carbon\Carbon::parse($cooldown_ends)->toIso8601String();
@endphp

<span class="cd-timer-pill" id="pill_{{ $timerId }}">
    <i class="fas fa-hourglass-half"></i>
    <span id="{{ $timerId }}">Calculating...</span>
</span>

<style>
    .cd-timer-pill {
        display: inline-flex; align-items: center; gap: 6px;
        background: #fff3cd; border: 1px solid #ffc107;
        border-radius: 20px; padding: 4px 14px;
        font-size: 13px; font-weight: 600; color: #7b5800;
    }
    .cd-timer-pill.expired {
        background: #d4edda; border-color: #28a745; color: #155724;
    }
</style>

<script>
(function() {
    const endTime = new Date("{{ $isoTime }}").getTime();
    const el      = document.getElementById("{{ $timerId }}");
    const pill    = document.getElementById("pill_{{ $timerId }}");
    const reload  = {{ $doReload ? 'true' : 'false' }};

    function tick() {
        const dist = endTime - Date.now();
        if (dist <= 0) {
            el.textContent   = 'Available Now!';
            pill.classList.add('expired');
            clearInterval(iv);
            if (reload) setTimeout(() => location.reload(), 1500);
            return;
        }
        const h = Math.floor(dist / 3600000);
        const m = Math.floor((dist % 3600000) / 60000);
        const s = Math.floor((dist % 60000) / 1000);
        el.textContent = (h > 0 ? h + 'h ' : '') + m + 'm ' + String(s).padStart(2,'0') + 's remaining';
    }

    tick();
    const iv = setInterval(tick, 1000);
})();
</script>