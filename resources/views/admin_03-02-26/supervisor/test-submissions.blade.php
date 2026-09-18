@extends('layout.admin.app')

@section('content')
    <div class="content-card">
        <div class="div-main-haeds">
            <h2 class="page-title">Supervisor Detail</h2>
        </div>
        @if ($sup)
            <div class="job-details-box">
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Supervisor ID</label>
                        <p>Sup-{{ $sup->id }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Name</label>
                        <p>{{ ucfirst($sup->user->name) }}</p>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <label>Email</label>
                        <p>{{ $sup->user->email }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Phone</label>
                        <p>{{ $sup->user->phone }}</p>
                    </div>


                    <div class="detail-item">
                        <label>Address</label>
                        <p>{{ $sup->address }}, {{ $sup->address }}, {{ $sup->city }}, {{ $sup->state->name }},
                            {{ $sup->pincode }}</p>
                    </div>

                    <div class="detail-item">
                        <label>Aadhar Card</label>
                        <p>
                            @if ($sup->aadhar_file)
                                <a href="{{ asset('storage/' . $sup->aadhar_file) }}" target="_blank"
                                    class="btn btn-view-aadhar">
                                    <i class="fas fa-file-pdf"></i> View Current Aadhar
                                </a>
                            @endif
                        </p>
                    </div>


                    <div class="detail-item">
                        <label>Own a Bike</label>
                        <p>
                            @if($sup->own_bike)

                            Yes

                            @else

                            No

                            @endif
                        </p>
                    </div>

                    <div class="detail-item">
                        <label>Own a Phone</label>
                        <p>
                             @if($sup->own_phone)

                            Yes

                            @else

                            No

                            @endif
                        </p>
                    </div>



                    <div class="detail-item">
                        <label>Skills</label>

                        @foreach ($sup->skills as $skill)
                            <span class="skill-badge">{{ $skill->name }} @if (!$loop->last)
                                    ,
                                @endif
                            </span>
                        @endforeach

                    </div>

                    <div class="detail-item">
                        <label>Experience</label>

                        @foreach ($sup->experiences as $experience)
                            <span class="skill-badge">{{ $experience->name }} @if (!$loop->last)
                                    ,
                                @endif
                            </span>
                        @endforeach

                    </div>
                    <div class="detail-item">
                        <label>Preferred Work Locations</label>

                        @foreach ($sup->workLocations as $workLocation)
                            <span class="skill-badge">{{ $workLocation->name }} @if (!$loop->last)
                                    ,
                                @endif
                            </span>
                        @endforeach

                    </div>

                </div>
            </div>
            <br>
        @endif

        <div class="div-main-haeds">
            <h2 class="page-title">Attempted Tests</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <div class="table-container">
            <table class="supervisor-table">
                <thead>
                    <tr>

                        <th>Name</th>
                        <th>Email</th>
                        <th>Test Name</th>
                        <th>Score</th>
                        <th>Passing Marks</th>
                        <th>Percentage</th>
                        <th>Submitted At</th>
                        <th>Submission Type</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($testAttempts as $attempt)
                        <tr>

                            <td>{{ ucfirst($sup->user->name) ?? 'N/A' }}</td>
                            <td>{{ $sup->user->email ?? 'N/A' }}</td>
                            <td>{{ ucfirst($attempt->test_name) }}</td>
                            <td>
                                <strong>{{ $attempt->score_obtained }}</strong> / {{ $attempt->total_marks }}
                            </td>
                            <td>{{ $attempt->passing_marks }}</td>
                            <td>
                                <span
                                    class="badge text-white {{ $attempt->percentage >= 50 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            </td>
                            <td>{{ $attempt->submitted_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <span
                                    class="badge text-white {{ $attempt->test_submission_status === 'automatic' ? 'bg-warning' : 'bg-info' }}">
                                    {{ ucfirst($attempt->test_submission_status) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge text-white
                                @if ($attempt->status === 'pass') bg-success 
                                @elseif($attempt->status === 'fail') bg-danger 
                                @else bg-secondary @endif">
                                    {{ ucfirst($attempt->status == 'underReview' ? 'Under Review' : $attempt->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.test-review.show', $attempt->id) }}"
                                    class="btn-view add-news-reviews">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td colspan="10" class="text-center text-danger">No submissions under review</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $testAttempts->links() }}
        </div>
    </div>
@endsection
