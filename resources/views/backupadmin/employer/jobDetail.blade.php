@extends('layout.admin.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
    <div class="content-card">
        <div class="div-main-haeds">
            <h2 class="page-title">Job Detail</h2>
        </div>
        <div class="job-details-box">
            <div class="detail-row">
                <div class="detail-item">
                    <label>Job ID</label>
                    <p>JOB#{{ $job->id }}</p>
                </div>
                <div class="detail-item">
                    <label>Title</label>
                    <p>{{ ucfirst($job->title) }}</p>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>Total Experience</label>
                    <p>{{ $job->years }}</p>
                </div>
                <div class="detail-item">
                    <label>Job Posted On</label>
                    <p>{{ $job->created_at->format('d-m-Y') }}</p>
                </div>

                <div class="detail-item">
                    <label>Status</label>
                    @if ($job->status)
                        <p class="text-success">Active</p>
                    @else
                        <p class="text-danger">Inactive</p>
                    @endif
                </div>


            


                <div class="detail-item">
                    <label>Desired Skills</label>

                    @foreach ($job->skills as $skill)
                        <span class="skill-badge">{{ $skill->name }}</span>
                    @endforeach

                </div>

                <div class="detail-item">
                    <label>Desired Experience</label>

                    @foreach ($job->experiences as $experience)
                        <span class="skill-badge">{{ $experience->name }} @if(!$loop->last), @endif</span>
                    @endforeach

                </div>

                    <div class="detail-item">
                    <label>Description</label>

                    <p>{{ $job->description }}</p>

                </div>

            </div>
        </div>
        <br>
        <div class="div-main-haeds">
            <h2 class="page-title">Job Applications List</h2>
        </div>
        <div class="table-container">
            <table class="supervisor-table">
                <thead>
                    <tr>
                        <th>Phone No.</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>Preferred Work Locations</th>
                        <th>Own a Bike</th>
                        <th>Own a Phone</th>
                        <th>Skills</th>
                        <th>Action</th>
                        </tr>
                </thead>
                <tbody>

                   @forelse($job->appliedSupervisors as $sup)
                        <tr>

                            {{-- Phone --}}
                            <td><b>{{ $sup->user->phone ?? 'N/A' }}</b></td>

                            {{-- Name --}}
                            <td>{{ ucfirst($sup->user->name) ?? 'N/A' }}</td>

                            {{-- City --}}
                            <td>{{ $sup->city ?? 'N/A' }}</td>

                            {{-- Preferred Work Locations --}}
                            <td>
                                @if ($sup->workLocations && $sup->workLocations->count())
                                    {{ $sup->workLocations->pluck('name')->implode(', ') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            {{-- Own Bike --}}
                            <td>
                                @if ($sup->own_bike == 1)
                                    <span class="status-badge">Yes</span>
                                @else
                                    <span class="badge-no">No</span>
                                @endif
                            </td>

                            <td>
                                @if ($sup->own_phone == 1)
                                    <span class="status-badge">Yes</span>
                                @else
                                    <span class="badge-no">No</span>
                                @endif
                            </td>

                            {{-- Skills --}}
                            <td>
                                @if ($sup->skills && $sup->skills->count())
                                    {{ $sup->skills->pluck('name')->implode(', ') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>

                            <td class="view-profiles">
                                 <a href="{{ route('admin.test-review.index', $sup->id) }}"
                                    class="btn-view">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">
                                No supervisors have applied yet.
                            </td>
                        </tr>
                    @endforelse

                    </tbody>
                </table>
            </div>
        </div>

    @endsection
