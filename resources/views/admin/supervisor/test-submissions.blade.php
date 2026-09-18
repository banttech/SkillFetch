@extends('layout.admin.app')

@section('content')

    <style>
        .bg-secondary {
            background-color: #00466d !important;
            color: #ffffff;
        }

        .supervisor-table thead th {
            min-width: 158px;
        }
    </style>

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
                        <p>{{ $sup->address }}, {{ $sup->city }}, {{ $sup->state->name }}, {{ $sup->pincode }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Aadhar Card</label>
                        <p>
                            @if ($sup->aadhar_file)
                                <a href="{{ asset('storage/' . $sup->aadhar_file) }}" target="_blank"
                                    class="btn btn-view-aadhar">
                                    <i class="fas fa-file-pdf"></i> View Aadhar
                                </a>
                            @endif
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>Own a Bike</label>
                        <p>{{ $sup->own_bike ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Own a Phone</label>
                        <p>{{ $sup->own_phone ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Skills</label>
                        @foreach ($sup->skills as $skill)
                            <span class="skill-badge">{{ $skill->name }}{{ !$loop->last ? ',' : '' }}</span>
                        @endforeach
                    </div>
                    <div class="detail-item">
                        <label>Experience</label>
                        @foreach ($sup->experiences as $exp)
                            <span class="skill-badge">{{ $exp->name }}{{ !$loop->last ? ',' : '' }}</span>
                        @endforeach
                    </div>
                    <div class="detail-item">
                        <label>Preferred Work Locations</label>
                        @foreach ($sup->workLocations as $loc)
                            <span class="skill-badge">{{ $loc->name }}{{ !$loop->last ? ',' : '' }}</span>
                        @endforeach
                    </div>
                    <div class="detail-item">
                        <label>Salary (In Rs.) (Per Month)</label>
                        <span class="skill-badge">Rs. {{ $sup->salary }}</span>
                    </div>
                </div>
            </div>
            <br>
        @endif


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        {{-- Filter by test --}}
        @php
            $testIds = $testAttempts->getCollection()->pluck('test_id')->unique()->filter();
            $testNames = \App\Models\Test::whereIn('id', $testIds)->pluck('name', 'id');
        @endphp

        <div class="name-div-tested">


            <h2 class="page-title">Attempted Tests</h2>
            @if ($testNames->count() > 1)
                <div class="test-attempted">
                    {{-- <label style="font-size:14px;color:#475569;font-weight:500;">Filter by Test:</label> --}}
                    <select id="testFilter" class="custom-input form-control" onchange="filterByTest(this.value)">
                        <option value="">All Tests</option>
                        @foreach ($testNames as $tid => $tname)
                            <option value="{{ $tid }}">{{ $tname }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="table-container">
            <table class="supervisor-table">
                <thead>
                    <tr>

                        <th>Test Name</th>
                        <th>Score</th>
                        {{-- <th>Passing Marks</th> --}}
                        <th>Percentage</th>
                        <th>Submission</th>
                        <th>Time Taken</th>
                        <th>Suggested Skills by Admin</th>
                        <th>Rejection Reason</th>
                        <th>Result</th>
                        <th>Submitted At</th>
                        <th>Action</th>
                        <th>Dept. Wise Score</th>
                    </tr>
                </thead>
                <tbody id="attemptsTableBody">
                    @forelse($testAttempts as $attempt)
                        <tr data-test-id="{{ $attempt->test_id }}">

                            <td>
                                {{ ucfirst($attempt->test_name) }}
                                @if ($attempt->test_id)
                                    {{-- <span class="badge bg-secondary" style="font-size:10px;margin-left:4px;">
                                        #{{ $attempt->test_id }}
                                    </span> --}}
                                @endif
                            </td>
                            <td><strong>{{ $attempt->score_obtained }}</strong> / {{ $attempt->total_marks }}</td>
                            {{-- <td>{{ $attempt->passing_marks }}</td> --}}
                            <td>
                                <span
                                    class="badge text-white {{ $attempt->percentage >= 50 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge text-white {{ $attempt->test_submission_status === 'automatic' ? 'bg-warning' : 'bg-info' }}">
                                    {{ ucfirst($attempt->test_submission_status) }}
                                </span>
                            </td>
                            <td style="font-weight:600;white-space:nowrap;">{{ $attempt->formatted_duration }}</td>

                            <td>
                                @if ($attempt->testSkills && $attempt->testSkills->count() > 0)
                                    @foreach ($attempt->testSkills as $ts)
                                        <span class="badge bg-secondary"
                                            style="margin:1px 0;display:inline-block;">{{ $ts->skill->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($attempt->status === 'fail' && $attempt->reject_reason)
                                    <span class="text-danger" style="font-size: 14px;">{{ $attempt->reject_reason }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span
                                    class="badge text-white
                                    @if ($attempt->status === 'pass') bg-success
                                    @elseif($attempt->status === 'fail')    bg-danger
                                    @elseif($attempt->status === 'underReview') bg-warning
                                    @else bg-secondary @endif">
                                    {{ ucfirst($attempt->status === 'underReview' ? 'Under Review' : $attempt->status) }}
                                </span>
                            </td>
                            <td>{{ $attempt->submitted_at->format('d M Y, h:i A') }}</td>

                            <td>
                                <a href="{{ route('admin.test-review.show', $attempt->id) }}"
                                    class="btn-view add-news-reviews">
                                    <i class="fas fa-eye"></i> Review
                                </a>
                            </td>
                            <td>
                                <button class="btn-dept-score"
                                    onclick="DeptScorePopup.open('{{ route('test.dept-score', $attempt->id) }}')"
                                    title="View Department-wise Score">
                                    <i class="fas fa-chart-bar"></i> Dept. Wise Score
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-danger">No test submissions found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $testAttempts->links() }}
        </div>
    </div>

    <script>
        function filterByTest(testId) {
            const rows = document.querySelectorAll('#attemptsTableBody tr[data-test-id]');
            rows.forEach(row => {
                if (!testId || row.dataset.testId === testId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endsection
