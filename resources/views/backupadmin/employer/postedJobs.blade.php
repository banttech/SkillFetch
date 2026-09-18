@extends('layout.admin.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
    <div class="content-card">
        <div class="div-main-haeds">
            <h2 class="page-title">Employer Detail</h2>
        </div>
        <div class="job-details-box">
            <div class="detail-row">
                <div class="detail-item">
                    <label>Emp ID</label>
                    <p>Emp-{{ $employer->id }}</p>
                </div>
                <div class="detail-item">
                    <label>Email</label>
                    <p>{{ $employer->user->email }}</p>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-item">
                    <label>Name</label>
                    <p>{{ ucfirst($employer->user->name) }}</p>
                </div>
                <div class="detail-item">
                    <label>Mobile No.</label>
                    <p>{{ $employer->user->phone }}</p>
                </div>
            </div>
        </div>
        <br>
        <div class="div-main-haeds">
            <h2 class="page-title">Posted Jobs List</h2>
        </div>
        <div class="table-container">
            <table class="supervisor-table">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Title</th>
                        <th>Desired Skills</th>
                        <th>Exp. (In Yrs)</th>
                        <th>Status</th>
                        <th>Job Posted On</th>
                        <th>Action</th>
                </thead>
                <tbody>

                    @forelse ($jobs as $job)
                        <tr>
                            <td><strong>
                                    <a href="{{route('employer.jobApplication',$job->id)}}" style="text-decoration:none;">
                                        JOB #{{ $job->id }}
                                    </a>
                                </strong>
                            </td>
                            <td>{{ $job->title }}</td>
                            <td class="skills">
                                @if ($job->skills->count())
                                    @foreach ($job->skills as $skill)
                                        <span class="skill-badge">{{ $skill->name }}@if (!$loop->last)
                                                ,
                                            @endif
                                        </span>
                                    @endforeach
                                @else
                                    <span class="skill-badge">No Skills</span>
                                @endif
                            </td>
                            <td>{{ $job->years }}</td>
                            <td>
                                @if ($job->status)
                                    <span class="text-success">Active</span>
                                @else
                                    <span class="text-danger">Inactive</span>
                                @endif
                                </span>
                            </td>
                            <td>{{ $job->created_at->format('d-m-Y') }}</td>
                            <td><a href="{{route('employer.jobApplication',$job->id)}}" class="btn-view">View Job Application <span
                                        class="{{ count($job->appliedSupervisors) == 0 ? 'text-danger' : 'text-success' }}">({{ count($job->appliedSupervisors) }})</span></a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="color:red" class="text-center">No jobs found.</td>
                            </tr>
                        @endforelse



                    </tbody>
                </table>
            </div>
        </div>

    @endsection
