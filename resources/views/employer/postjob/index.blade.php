@extends('layout.employer.app')

@section('page-header')
    <style>
        .btn-primary {
            color: #fff;
            background-color: #003459;
            border-color: #007bff;
            font-size: 12px;
        }
    </style>


     <div class="content-header">
        <h2>
           <i class="fas fa-user-tie"></i>
            My Jobs
        </h2>

        <div>
            <a href="{{ route('employer.job.create') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Post New Job
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="job-card">

        @if (Session::has('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @endif

        @if (Session::has('success'))
            <div class="alert alert-success">
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table job-table">
                <thead>
                    <tr>
                        <th>JOB ID</th>
                        <th>TITLE</th>
                        {{-- <th>DESIRED SKILLS</th> --}}
                        <th>TOTAL EXPERIENCE</th>
                        {{-- <th>TOTAL EXPERIENCE</th> --}}
                        <th>TOTAL APPLICANTS</th>
                        <th>STATUS</th>
                        <th>PAYMENT STATUS</th>
                        <th>POSTED ON</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            {{-- Job ID --}}
                            <td class="job-id">
                                <a href="{{ route('employer.job.details', $job->id) }}" style="text-decoration:none;">
                                    JOB #{{ $job->id }}
                                </a>
                            </td>

                            {{-- Title --}}
                            <td>{{ $job->title }}</td>

                            {{-- Skills --}}
                            {{-- <td>
                                @if ($job->skills->count())
                                    @foreach ($job->skills as $skill)
                                        <span class="skill-badge">{{ $skill->name }}</span>
                                    @endforeach
                                @else
                                    <span class="skill-badge">No Skills</span>
                                @endif
                            </td> --}}

                            {{-- <td>
                                @if ($job->experiences->count())
                                    @foreach ($job->experiences as $experience)
                                        <span class="skill-badge">{{ $experience->name }}</span>
                                    @endforeach
                                @else
                                    <span class="skill-badge">No experiences</span>
                                @endif
                            </td> --}}

                            {{-- Experience --}}
                            <td>
                                @if ($job->years)
                                    <span class="skill-badge">{{ $job->years }}</span>
                                @else
                                    <span class="skill-badge">No Experience</span>
                                @endif
                            </td>

                            {{-- Total Applicants --}}
                            <td class="{{ $job->appliedSupervisors->count() ? 'text-success' : 'text-danger' }}">
                                <a href="{{ route('employer.job.details', $job->id) }}"
                                    class="text-reset" style="text-decoration-line: underline">
                                    {{ $job->appliedSupervisors->count() ?? '0' }}
                                </a>
                            </td>


                            {{-- Job Status --}}
                            <td>
                                @if ($job->status)
                                    <span class="text-success">Active</span>
                                @else
                                    <span class="text-danger">Inactive</span>
                                @endif
                            </td>



                            {{-- Payment --}}
                            <td>
                                @if ($job->paymentStatus === 'paid')
                                    <span class="text-success">Paid</span>
                                    {{-- Paid - Show nothing --}}
                                @else
                                    @include('components.razorpay-payment-button', [
                                        'buttonId' => 'payBtn_' . $job->id,
                                        'buttonText' => 'Pay Now',
                                        'initiateRoute' => route('employer.job-payment.initiate'),
                                        'redirectUrl' => '/employer/myjobs',
                                        'paymentName' => 'Job Payment',
                                        'requestBody' => [
                                            'job_id' => $job->id,
                                        ],
                                    ])
                                @endif
                            </td>

                            {{-- Created at --}}
                            <td>{{ $job->created_at->format('d-m-Y') }}</td>

                            {{-- Actions --}}
                            <td>
                                @if($job->appliedSupervisors->count() > 0)
                                    <span class="action-btn text-muted" title="Your job is already published so you can't edit" style="cursor: not-allowed; opacity: 0.5;">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                @else
                                    <a href="{{ route('employer.job.edit', $job->id) }}" class="action-btn">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-danger">No Jobs Posted Yet</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
@endsection



