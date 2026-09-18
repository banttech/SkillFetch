@extends('layout.supervisor.app')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .custom-radio {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            color: #333;
            position: relative;
        }

        /* Hide default radio */
        .custom-radio input {
            display: none;
        }

        /* Outer circle */
        .radio-mark {
            width: 20px;
            height: 20px;
            border: 2px solid #003673;
            border-radius: 50%;
            position: relative;
            transition: all 0.3s ease;
        }

        /* Inner dot */
        .radio-mark::after {
            content: "";
            width: 10px;
            height: 10px;
            background: #003673;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.3s ease;
        }

        /* Checked state */
        .custom-radio input:checked+.radio-mark::after {
            transform: translate(-50%, -50%) scale(1);
        }

        /* Hover effect */
        .custom-radio:hover .radio-mark {
            box-shadow: 0 0 0 4px rgba(0, 54, 115, 0.15);
        }

        .profile-img-preview {
            width: 82px;
            height: 82px;
            object-fit: cover;
            border: 2px solid #00456c;
            border-radius: 8px;
            padding: 4px;
        }

        .text-muted {
            color: #ff750ff5 !important;
        }

        a.btn.btn-view-aadhar {
            background: #0e253a;
            color: #fefefe;
            font-weight: 500;
            position: absolute;
            top: 38px;
            right: 22px;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .custom-radio {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            cursor: pointer;
        }

        .custom-radio input[type="radio"] {
            margin-right: 8px;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #00466d;
        }

        .required-star {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #407491;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
            height: 48px;
            font-size: 15px;
            font-weight: 500;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #00466d;
            outline: 0;
            box-shadow: 0 0 0 .2rem rgb(0 70 109 / 27%);
        }

        .select2-container--default .select2-selection--multiple {
            border: 1px solid #407491 !important;
            border-radius: 6px !important;
            font-size: 14px !important;
            transition: border-color 0.2s !important;
            min-height: 48px !important;
            padding: 6px !important;
        }

        button.btn.btn-uppdate-profile {
            background: #0e253a;
            color: #fff;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 15px;
            letter-spacing: .3px;
            padding: 8px 16px;
            min-width: 179px;
        }

        a.btn.btn-cancel-s {
            background: #d20000;
            color: #fff;
            font-weight: 600;
            min-width: 179px;
        }

        .div-fl-buttons {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 18px;
        }
    </style>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="p-4">
                        <div class="welcome-box2">
                            <h1>Edit Profile</h1>
                        </div>
                        <div class="job-detail-container">
                            <div class="job-detail-card">
                                <div class="job-detail-card-border"></div>

                                <form action="{{ route('supervisor.profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    {{-- NAME & EMAIL --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Name <span class="required-star">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', $user->name) }}"
                                                oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')">
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email <span class="required-star">*</span></label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $user->email) }}">
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- PROFILE IMAGE & MOBILE --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Profile Image (200 × 200)</label>
                                            <input type="file" name="image" class="form-control"
                                                accept="image/jpeg,image/jpg,image/png">
                                            <p class="mt-1 text-muted small">Only JPG, JPEG, PNG allowed (200×200px)</p>

                                            @php
                                                $image = $user->image
                                                    ? asset('supervisor_assets/images/' . $user->image)
                                                    : asset('supervisor_assets/images/user-dummy.png');
                                            @endphp

                                            <img src="{{ $image }}" id="previewImage"
                                                class="profile-img-preview mt-2" alt="profile" width="50"
                                                height="50">

                                            @error('image')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Mobile <span class="required-star">*</span></label>
                                            <input type="text" name="mobile" class="form-control"
                                                value="{{ $user->phone }}" readonly disabled
                                                style="background-color: #e9ecef; cursor: not-allowed;">
                                            <small class="text-muted">Mobile number cannot be changed</small>
                                        </div>
                                    </div>

                                    {{-- SKILLS --}}
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Select Your Skills <span
                                                    class="required-star">*</span></label>
                                            <select name="skills[]" id="skills" multiple class="form-control select2">
                                                @foreach ($skills as $skill)
                                                    <option value="{{ $skill->id }}"
                                                        {{ in_array($skill->id, old('skills', $supervisor->skills->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $skill->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('skills')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>


                                    {{-- EXPERIENCE --}}
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Experience <span
                                                    class="required-star">*</span></label>
                                            <select name="experiences[]" id="experiences" multiple
                                                class="form-control select2">
                                                @foreach ($experiences as $exp)
                                                    <option value="{{ $exp->id }}"
                                                        {{ in_array($exp->id, old('experiences', $supervisor->experiences->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $exp->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('experiences')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- WORK LOCATIONS --}}
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Preferred Work Locations <span
                                                    class="required-star">*</span></label>
                                            <select name="locations[]" id="locations" multiple class="form-control select2">
                                                @foreach ($locations as $loc)
                                                    <option value="{{ $loc->id }}"
                                                        {{ in_array($loc->id, old('locations', $supervisor->workLocations->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('locations')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                      <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Current or last drawn Salary (In Rs.) (Per Month) <span class="required-star">*</span></label>
                                            <input type="text" name="salary" class="form-control"
                                                value="{{ old('salary', $supervisor->salary) }}"
                                                placeholder="Enter Your Current salary" step="1" min="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            @error('salary')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- ADDRESS --}}
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address <span class="required-star">*</span></label>
                                            <input type="text" name="address" class="form-control"
                                                value="{{ old('address', $supervisor->address) }}"
                                                placeholder="Enter Your Address">
                                            @error('address')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- CITY, STATE, PINCODE --}}
                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label class="form-label">City <span class="required-star">*</span></label>
                                            <input type="text" name="city" class="form-control"
                                                value="{{ old('city', $supervisor->city) }}"
                                                oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')"
                                                placeholder="Enter Your City">
                                            @error('city')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">State <span class="required-star">*</span></label>
                                            <select name="state_id" class="form-control">
                                                <option value="">Select State</option>
                                                @foreach ($states as $state)
                                                    <option value="{{ $state->id }}"
                                                        {{ old('state_id', $supervisor->state_id) == $state->id ? 'selected' : '' }}>
                                                        {{ $state->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('state_id')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Pin Code <span
                                                    class="required-star">*</span></label>
                                            <input type="text" name="pincode" class="form-control"
                                                value="{{ old('pincode', $supervisor->pincode) }}" maxlength="6"
                                                oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                                                placeholder="Enter Pincode">
                                            @error('pincode')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- AADHAR UPLOAD --}}
                                    <div class="row">
                                        <div class="col-md-12 mb-3 position-relative">
                                            <label class="form-label">Upload Aadhar Card</label>
                                            <input type="file" name="aadhar" class="form-control"
                                                accept=".pdf,.doc,.docx,image/jpeg,image/jpg,image/png,image/webp">
                                            <small class="text-muted">Only .pdf, .doc, .docx, .png, .jpg, .jpeg, .webp file types are allowed. File size must not exceed 2
                                            MB.</small>

                                            @if ($supervisor->aadhar_file)
                                                <div class="mt-2">
                                                    <a href="{{ asset('storage/' . $supervisor->aadhar_file) }}"
                                                        target="_blank" class="btn btn-view-aadhar">
                                                        <i class="fas fa-file-pdf"></i> View Current Aadhar
                                                    </a>
                                                </div>
                                            @endif

                                            @error('aadhar')
                                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>


                                    {{-- OWN BIKE --}}
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Own A Bike</label>
                                            <div class="radio-group">
                                                <label class="custom-radio">
                                                    <input type="radio" name="own_bike" value="Yes"
                                                        {{ old('own_bike', $supervisor->own_bike ? 'Yes' : 'No') === 'Yes' ? 'checked' : '' }}>
                                                    <span class="radio-mark"></span>
                                                    Yes
                                                </label>
                                                <label class="custom-radio">
                                                    <input type="radio" name="own_bike" value="No"
                                                        {{ old('own_bike', $supervisor->own_bike ? 'Yes' : 'No') === 'No' ? 'checked' : '' }}>
                                                    <span class="radio-mark"></span>
                                                    No
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Own A Phone</label>
                                            <div class="radio-group">
                                                <label class="custom-radio">
                                                    <input type="radio" name="own_phone" value="Yes"
                                                        {{ old('own_phone', $supervisor->own_phone ? 'Yes' : 'No') === 'Yes' ? 'checked' : '' }}>
                                                    <span class="radio-mark"></span>
                                                    Yes
                                                </label>
                                                <label class="custom-radio">
                                                    <input type="radio" name="own_phone" value="No"
                                                        {{ old('own_phone', $supervisor->own_phone ? 'Yes' : 'No') === 'No' ? 'checked' : '' }}>
                                                    <span class="radio-mark"></span>
                                                    No
                                                </label>
                                            </div>
                                        </div>
                                    </div>



                                    {{-- SUBMIT BUTTON --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="div-fl-buttons">
                                                <button type="submit" class="btn btn-uppdate-profile">
                                                    <i class="fas fa-save"></i> Update Profile
                                                </button>
                                                <a href="{{ route('supervisor.dashboard') }}" class="btn btn-cancel-s">
                                                    <i class="fas fa-times"></i> Cancel
                                                </a>
                                            </div>
                                        </div>
                                    </div>


                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                placeholder: "Select options",
                width: '100%'
            });

            // Image Preview
            document.querySelector('input[name="image"]').addEventListener('change', function() {
                const file = this.files[0];
                const img = document.getElementById("previewImage");

                if (file && img) {
                    const reader = new FileReader();
                    reader.onload = (e) => img.src = e.target.result;
                    reader.readAsDataURL(file);
                }
            });

            // Auto-dismiss alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });
    </script>
@endsection
