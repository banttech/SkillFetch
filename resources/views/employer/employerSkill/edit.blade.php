@extends('layout.employer.app')

@section('content')

    <div class="orker-job-form-card">
        <h2 class="orker-page-title">Edit Skill</h2>

        <form id="skillForm" action="{{ route('employer.skill.update', $skill->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="orker-form-group">
                <label class="orker-form-label">Skill<small class="text-danger">*</small></label>
                <input type="text" class="form-input " name="skills" value="{{ old('skills', $skill->skills) }}" placeholder="Enter skill">
                @error('skills')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="orker-btn-submit">Update Skill</button>

        </form>
    </div>

@endsection
