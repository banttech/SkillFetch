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
            <i class="fas fa-tools"></i>
            Employer Skills
        </h2>

        <div>
            <a href="{{ route('employer.skill.create') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Post New Skill
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
                        {{-- <th>ID</th> --}}
                        <th>SKILLS</th>
                        <th>CREATED AT</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($skills as $skill)
                        <tr>
                            {{-- <td class="job-id">
                                #{{ $skill->id }}
                            </td> --}}

                            <td>{{ $skill->skills }}</td>

                            <td>{{ $skill->created_at->format('d-m-Y') }}</td>

                            <td>
                                <a href="{{ route('employer.skill.edit', $skill->id) }}" class="action-btn">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-danger">No Skills Posted Yet</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
@endsection
