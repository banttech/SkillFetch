@extends('layout.employer.app')


@section('content')

    <!-- SELECT2 CSS -->

    <div class="orker-job-form-card">
        <h2 class="orker-page-title">Post New Job</h2>

        <form id="jobForm" action="{{ route('employer.job.store') }}" method="POST">
            @csrf

            {{-- Job Title --}}
            <div class="orker-form-group">
                <label class="orker-form-label">Job Title<small class="text-danger">*</small></label>
                <input type="text" class="orker-form-control" name="title" placeholder="Enter job title">
                @error('title')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Description --}}
           <div class="orker-form-group">
                <label class="orker-form-label">Description<small class="text-danger">*</small></label>
                <textarea class="form-input orker-form-textarea" name="description" rows="4"
                    placeholder="Enter job description">{{ old('description') }}</textarea>
                @error('description')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            {{-- Skills & Experience --}}
            <div class="orker-form-row">

                {{-- Skills --}}
                <div class="orker-form-col">
                    <label class="orker-form-label">Skills Needed<small class="text-danger">*</small></label>
                    <select class="orker-form-control select2" name="skills[]" multiple data-placeholder="Select Skills">
                        @foreach($skills as $skill)
                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                        @endforeach
                    </select>
                    @error('skills')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Experience --}}
                <div class="orker-form-col">
                    <label class="orker-form-label">Experience<small class="text-danger">*</small></label>
                    <select class="orker-form-control select2" name="experience_details[]" multiple data-placeholder="Select Experience">
                        @foreach($experiences as $exp)
                            <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                        @endforeach
                    </select>
                    @error('experience_details')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            {{-- Years & Status --}}
            <div class="orker-form-row">
                <div class="orker-form-col">
                    <label class="orker-form-label">Total Years of Experience<small class="text-danger">*</small></label>
                    {{-- <input type="text" class="orker-form-control" name="years" placeholder="e.g. 3 years"> --}}

                     <select class="orker-form-select" name="years">
                        <option value="">Select total years of experience</option>
                        <option value="0-1 Year">0-1 Year</option>
                        <option value="1-3 Year">1-3 Year</option>
                        <option value="3-5 Year">3-5 Year</option>
                        <option value="5-6 Year">5-6 Year</option>
                        <option value="6-7 Year">6-7 Year</option>
                        <option value="7-8 Year">7-8 Year</option>
                        <option value="8-9 Year">8-9 Year</option>
                        <option value="9-10 Year">9-10 Year</option>
                        <option value="10+ Year">10+ Year</option>

                    </select>
                    
                    @error('years')
                        <span class="text-danger">{{ $message }}</span> 
                    @enderror
                </div>

                <div class="orker-form-col">
                    <label class="orker-form-label">Status<small class="text-danger">*</small></label>
                    <select class="orker-form-select" name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                    @error('status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="orker-btn-submit">Post Job</button>

        </form>
    </div>




@endsection