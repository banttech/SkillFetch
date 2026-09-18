@extends('layout.employer.app')

@section('title', $page_title ?? 'Edit Job')

@section('content')

<div class="orker-job-form-card">
    <h2 class="orker-page-title">Edit Job</h2>

    <form id="jobForm" action="{{ route('employer.job.update', $job->id) }}" method="POST">
        @csrf

        {{-- Job Title --}}
        <div class="orker-form-group">
            <label class="orker-form-label">Job Title</label>
            <input type="text" class="orker-form-control" name="title"
                value="{{ $job->title }}" required>
        </div>

        {{-- Description --}}
        <div class="orker-form-group">
            <label class="orker-form-label">Description</label>
            <textarea class="orker-form-control orker-form-textarea"
                name="description" rows="4" required>{{ $job->description }}</textarea>
        </div>

        {{-- Skills & Experience --}}
        <div class="orker-form-row">

            {{-- Skills --}}
            <div class="orker-form-col">
                <label class="orker-form-label">Skills Needed</label>
                <select class="orker-form-control select2" name="skills[]" multiple style="height:140px;">
                    @foreach($skills as $skill)
                        <option value="{{ $skill->id }}"
                            {{ in_array($skill->id, $selectedSkills) ? 'selected' : '' }}>
                            {{ $skill->name }}
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Hold CTRL to select multiple</small>
            </div>

            {{-- Experience --}}
            <div class="orker-form-col">
                <label class="orker-form-label">Experience</label>
                <select class="orker-form-control select2" name="experience_details[]" multiple style="height:140px;">
                    @foreach($experiences as $exp)
                        <option value="{{ $exp->id }}"
                            {{ in_array($exp->id, $selectedExperiences) ? 'selected' : '' }}>
                            {{ $exp->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Years & Status --}}
        <div class="orker-form-row">

            <div class="orker-form-col">
                <label class="orker-form-label">Years of Experience</label>
                <input type="text" class="orker-form-control" name="years"
                    value="{{ $job->years }}" required>
            </div>

            <div class="orker-form-col">
                <label class="orker-form-label">Status</label>
                <select class="orker-form-select" name="status">
                    <option value="Active" {{ $job->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ $job->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

        </div>

        {{-- Submit --}}
        <button type="submit" class="orker-btn-submit">
            Update Job
        </button>

    </form>

</div>

@endsection
