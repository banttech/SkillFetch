@extends('layout.employer.app')

@section('page-header')
    <div class="content-header">
        <h2>
            <i class="fas fa-briefcase"></i>
            My Jobs
        </h2>

        {{-- <div>
            <a href="{{ route('employer.job.create') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Post New Job
            </a>
        </div> --}}
    </div>
@endsection

@section('content')

    <div class="job-card">
        <div class="table-responsive">
            <table class="table job-table">
                <thead>
                    <tr>
                        <th>JOB ID</th>
                        <th>TITLE</th>
                        <th>DESIRED SKILLS</th>
                        <th>EXPERIENCE</th>
                        <th>STATUS</th>
                        <th>POSTED ON</th>
                        <th>PAYMENT STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            {{-- Job ID --}}
                            <td class="job-id">
                                <a href="{{ route('employer.job.details', $job->id) }}" style="text-decoration:none;">
                                    {{ $job->id }}
                                </a>
                            </td>

                            {{-- Title --}}
                            <td>{{ $job->title }}</td>

                            {{-- Skills --}}
                            <td>
                                @if ($job->skills->count())
                                    @foreach ($job->skills as $skillRow)
                                        <span class="skill-badge">{{ $skillRow->skill->name }}</span>
                                    @endforeach
                                @else
                                    <span class="skill-badge">No Skills</span>
                                @endif
                            </td>

                            {{-- Experience --}}
                            <td>
                                @if ($job->years)
                                    <span class="skill-badge">{{ $job->years }}</span>
                                @else
                                    <span class="skill-badge">No Experience</span>
                                @endif
                            </td>


                            {{-- Job Status --}}
                            <td>
                                <span class="status-badge">{{ $job->status }}</span>
                            </td>

                            {{-- Created at --}}
                            <td>{{ $job->created_at->format('d-m-Y') }}</td>

                            {{-- Payment --}}
                            <td>
                                @if($job->payment && $job->payment->payment_status === 'paid')
                                    <span class="payment-status-badge">Paid</span>
                                    {{-- Paid - Show nothing --}}
                                @else
                                    <button onclick="payNow({{ $job->id }}, this)"
                                        style="background:#007bff;color:#fff;padding:6px 12px;
                                                                       border-radius:20px;font-size:12px;border:none;cursor:pointer;">
                                        Pay Now
                                    </button>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <a href="{{ route('employer.job.edit', $job->id) }}" class="action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No Jobs Posted Yet</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
@endsection


{{-- Razorpay Script --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    function payNow(job_id, btn) {

        btn.innerHTML = "Processing...";
        btn.disabled = true;

        fetch("{{ route('employer.job.fees') }}", {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "Accept": "application/json",
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ job_id: job_id })
        })
            .then(async res => {
                const text = await res.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    alert("Server error: " + text);
                    btn.innerHTML = "Pay Now";
                    btn.disabled = false;
                    throw new Error("Invalid JSON from server");
                }
            })
            .then(data => {
                if (!data.success) {
                    alert(data.message ?? "Unable to create payment order.");
                    btn.innerHTML = "Pay Now";
                    btn.disabled = false;
                    return;
                }

                var options = {
                    key: data.key,
                    amount: data.amount,
                    currency: "INR",
                    name: "Job Payment",
                    description: "Payment for Job #" + job_id,
                    order_id: data.order_id,

                    handler: function (response) {
                        alert("Payment successful! Waiting for verification...");
                        location.reload();
                    }
                };

                var rzp = new Razorpay(options);
                rzp.open();

                btn.innerHTML = "Pay Now";
                btn.disabled = false;
            })
            .catch(err => {
                alert("Something went wrong.");
                btn.innerHTML = "Pay Now";
                btn.disabled = false;
            });
    }
</script>