@extends('layout.employer.app')

@section('content')
    <!-- SELECT2 CSS -->

    @if(Session::has('error'))
        <div class="alert alert-danger">
            {{ Session::get('error') }} 
        </div>
    @endif

     @if(Session::has('success'))
        <div class="alert alert-danger">
            {{ Session::get('success') }} 
        </div>
    @endif

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="content-header">
        <h2>
           <i class="fas fa-user-plus"></i>
           Post New Job
        </h2>
    </div>

    <div class="orker-job-form-card create-page">
        <form id="jobForm" action="{{ route('employer.job.store') }}" method="POST">
            @csrf

            {{-- Job Title --}}
            <div class="orker-form-group">
                <label class="orker-form-label">Job Title<small class="text-danger">*</small></label>
                <select class="form-input orker-form-control job-title-select2" name="job_title_id">
                    <option value="">Select Job Title</option>
                    @foreach ($jobTitles as $jt)
                        <option value="{{ $jt->id }}" {{ old('job_title_id') == $jt->id ? 'selected' : '' }}>
                            {{ $jt->title }}
                        </option>
                    @endforeach
                </select>
                @error('job_title_id')
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
            <div class="row orker-form-group">
                {{-- Skills --}}
                <div class="col-md-6">
                    <div class="">
                        <label class="orker-form-label">Admin Skills<small class="text-danger">*</small></label>
                        <select class="form-input select2" name="skills[]" multiple data-placeholder="Select skills">

                            @foreach ($skills as $skill)
                                <option value="{{ $skill->id }}"
                                    {{ in_array($skill->id, old('skills', [])) ? 'selected' : '' }}>
                                    {{ $skill->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('skills')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- employer skills --}}

                  <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Employer Skills<small class="text-danger">*</small></label>
                        <select class="orker-form-control select2" name="employer_skills[]" multiple data-placeholder="Select employer skills">
                            @foreach ($employerSkills as $skill)
                                <option value="{{ $skill->id }}" {{ in_array($skill->id, old('employer_skills', [])) ? 'selected' : '' }}>
                                    {{ $skill->skills }}
                                </option>
                            @endforeach
                        </select>
                        @error('employer_skills')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

               
            </div>

            {{-- Employer Skills & Work Locations --}}
            <div class="row orker-form-group">
              
                 {{-- Experience --}}
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Experience<small class="text-danger">*</small></label>
                        <select class="orker-form-control select2" name="experience_details[]" multiple
                            data-placeholder="Select experience">
                            <option></option>
                            @foreach ($experiences as $exp)
                                <option value="{{ $exp->id }}"
                                    {{ in_array($exp->id, old('experience_details', [])) ? 'selected' : '' }}>
                                    {{ $exp->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('experience_details')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                 <div class="col-md-6 orker-form-col">
                    <label class="orker-form-label">Total Years of Experience<small class="text-danger">*</small></label>
                    @php
                        $yearsOptions = [
                            '0-1 Year', '1-2 Year', '2-3 Year', '3-4 Year', '4-5 Year',
                            '5-6 Year', '6-7 Year', '7-8 Year', '8-9 Year', '9-10 Year', '10+ Year',
                        ];
                    @endphp

                   <select class="form-input" name="years">
                        <option value="">Select total years of experience</option>
                        @foreach ($yearsOptions as $year)
                            <option value="{{ $year }}" {{ old('years') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    @error('years')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

             
            </div>

            {{-- Salary & Allowances --}}
            <div class="row orker-form-group">
                <div class="col-md-12">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Fixed Salary (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input" name="fixed_salary" placeholder="Integer only" value="{{ old('fixed_salary') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('fixed_salary') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
                <div class="row orker-form-group">
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Variable Salary From (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input" name="variable_salary_from" placeholder="Integer only" value="{{ old('variable_salary_from') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('variable_salary_from') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Variable Salary To (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input" name="variable_salary_to" placeholder="Integer only" value="{{ old('variable_salary_to') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('variable_salary_to') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="row orker-form-group mt-3">
                <div class="col-md-12">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Petrol Allowance (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input" name="petrol_allowance" placeholder="Integer only" value="{{ old('petrol_allowance') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('petrol_allowance') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row orker-form-group mt-3">
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Accommodation (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input" name="accommodation" placeholder="Integer only" value="{{ old('accommodation') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('accommodation') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
           
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Food Allowance (In Rs.) (Per Month)<small class="text-danger">*</small></label>
                        <input type="text" class="form-input " name="food_allowance" placeholder="Integer only" value="{{ old('food_allowance') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('food_allowance') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Locations & Status --}}
            <div class="row orker-form-group">
                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Preferred Work Locations<small class="text-danger">*</small></label>
                        <select class="orker-form-control select2" name="work_locations[]" multiple data-placeholder="Select locations">
                            @foreach ($workLocations as $loc)
                                <option value="{{ $loc->id }}" {{ in_array($loc->id, old('work_locations', [])) ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('work_locations')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="orker-form-col">
                        <label class="orker-form-label">Status<small class="text-danger">*</small></label>
                        <select class="form-input" name="status">
                            <option value="">Select Status</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="orker-btn-submit">Post Job</button>

        </form>
    </div>

    <!-- jQuery and Select2 script for tags -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // General select2
            $('.select2').select2();

            // Job Title Custom Dropdown
            $('.job-title-select2').select2({
                placeholder: "Select Job Title"
            }).on('select2:open', function () {
                // Check if our custom element is already there
                if (!$('.select2-results__add-new').length) {
                    var $searchBox = $('.select2-search.select2-search--dropdown');
                    
                    // Add the "Create New" banner
                    $searchBox.before(
                        '<div class="select2-results__add-new" style="padding: 10px 12px; cursor: pointer; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #003459; background: #fff; display: flex; align-items: center; gap: 8px; font-size: 14px; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor=\'#f1f5f9\'" onmouseout="this.style.backgroundColor=\'#fff\'">' +
                        '<i class="fas fa-plus" style="font-size: 12px;"></i> Create New Job Title' +
                        '</div>'
                    );

                    // Add the hidden form
                    var $inputForm = $('<div class="select2-results__new-form" style="display: none; padding: 15px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05);">' +
                        '<div style="margin-bottom: 10px;">' +
                        '<label style="    font-size: 14px;font-weight: 500;color: #101010;margin-bottom: 8px;display: block;letter-spacing: .2px;">Title Name</label>' +
                        '<input type="text" class="form-input form-control" style="width: 100%;border: 1px solid #cbd5e1;padding: 10px 14px;border-radius: 4px;font-size: 14px;box-sizing: border-box;letter-spacing: .2px;min-height: 50px;border-radius: 10px;border: 2px solid #cbd5e0;color: #2d3748;font-family:Poppins;" id="newJobTitleInput" placeholder="e.g. Senior Laravel Developer">' +
                        '<div id="newJobTitleError" style="color: #dc2626; font-size: 14px; margin-top: 4px; display: none; line-height: 1.4;margin-bottom:8px"></div>' +
                        '</div>' +
                        '<div style="display: flex; gap: 8px;">' +
                            '<button type="button" class="button-ad-top" onmouseover="this.style.opacity=\'0.9\'" onmouseout="this.style.opacity=\'1\'" id="btnCreateJobTitle">Create Title</button>' +
                            '<button type="button" class="button-cancel-new" onmouseover="this.style.backgroundColor=\'#f1f5f9\'" onmouseout="this.style.backgroundColor=\'#fff\'" id="btnCancelJobTitle">Cancel</button>' +
                        '</div>' +
                        '</div>');
                    
                    $('.select2-results__add-new').after($inputForm);

                    // Toggle form visibility
                    $('.select2-results__add-new').on('mouseup', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        $inputForm.slideToggle('fast', function() {
                            if($inputForm.is(':visible')){
                                $('#newJobTitleInput').focus();
                            } else {
                                $('#newJobTitleInput').val('');
                                $('#newJobTitleError').hide();
                            }
                        });
                    });

                    // Prevent closing select2 when clicking inside the form
                    $inputForm.on('mouseup', function(e) {
                        e.stopPropagation();
                    });

                    $('#btnCancelJobTitle').on('click', function(e){
                        e.stopPropagation();
                        $inputForm.slideUp('fast');
                        $('#newJobTitleInput').val('');
                        $('#newJobTitleError').hide();
                    });

                    $('#newJobTitleInput').on('keydown', function(e){
                         if(e.key === 'Enter'){
                             e.preventDefault();
                             $('#btnCreateJobTitle').click();
                         }
                    });

                    // Handle creation
                    $('#btnCreateJobTitle').on('click', function(e) {
                        e.stopPropagation();
                        $('#newJobTitleError').hide();
                        var newTitle = $('#newJobTitleInput').val().trim();
                        if(newTitle === "") {
                            $('#newJobTitleError').text('The title field is required.').slideDown();
                            return;
                        }
                        
                        var $btn = $(this);
                        $btn.prop('disabled', true).text('Creating...');
                        
                        $.ajax({
                            url: "{{ route('employer.job.title.store') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                title: newTitle
                            },
                            success: function(response) {
                                $btn.prop('disabled', false).text('Create Title');
                                if (response.success) {
                                    // Clear value & close form
                                    $('#newJobTitleInput').val('');
                                    $('#newJobTitleError').hide();
                                    $inputForm.hide(); // Hide instantly

                                    // Make new option and append
                                    var newOption = new Option(response.text, response.id, true, true);
                                    $('.job-title-select2').append(newOption).trigger('change');
                                    // Close the dropdown cleanly
                                    $('.job-title-select2').select2('close');
                                } else {
                                    $('#newJobTitleError').text("Error creating job title.").slideDown();
                                }
                            },
                            error: function(xhr) {
                                $btn.prop('disabled', false).text('Create Title');
                                if (xhr.status === 422) {
                                    let errors = xhr.responseJSON.errors;
                                    let errorText = '';
                                    $.each(errors, function (key, value) {
                                        errorText += value[0] + '<br>';
                                    });
                                    $('#newJobTitleError').html(errorText).slideDown();
                                } else {
                                    $('#newJobTitleError').text("An unexpected error occurred. Please try again.").slideDown();
                                }
                            }
                        });
                    });
                }
            });
        });
    </script>
@endsection
