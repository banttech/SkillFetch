@extends('layout.employer.app')

@section('content')

    <div class="orker-job-form-card">
        <h2 class="orker-page-title">Post New Skill</h2>

        <form id="skillForm" action="{{ route('employer.skill.store') }}" method="POST">
            @csrf

            <div class="orker-form-group">
                <label class="orker-form-label">Skill<small class="text-danger">*</small></label>
                <input type="text" class="orker-form-control" name="skills" placeholder="Enter skill">
                @error('skills')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="orker-btn-submit">Post Skill</button>

        </form>
    </div>

@endsection
