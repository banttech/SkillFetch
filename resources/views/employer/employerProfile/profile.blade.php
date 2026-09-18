@extends('layout.employer.app')

@section('content')

<style>
 .error-message {
        color: #dc3545;
        font-size: 13px;
        margin-top: 4px;
        display: block;
    }
    .input-error {
        border-color: #dc3545 !important;
    }
.preview-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border: 2px solid #ddd;
    border-radius: 8px;
    margin-top: 10px;
    padding: 5px;
}
    .upload-section {
        margin-top: 10px;
    }
    .file-info {
        margin-top: 8px;
        font-size: 14px;
        color: #666;
    }.orker-btn-submit:hover{
        color: #fff;
    }a.btn.orker-btn-submit.btn-color {
    background: #b53838;
}
  .current-file {
    padding: 12px 25px;
    background: #d6d6d6;
    /* border: 2px solid #00466d; */
    border-radius: 4px;
    margin-top: 8px;
    display: inline-block;
    color: #000;
    font-size: 17px;
    font-weight: 500;
} .current-file a {
    color: #00466d;
    font-weight: 600;
    padding-left: 6px;
}
.allowed_type {
    font-size: 14px;
    color: #f69340;
    margin-top: 5px;
}
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
    }
    .required-star {
        color: #dc3545;
    }.orker-form-group {
    margin-bottom: 0.5rem;
}   
        @media(max-width:768px){
                .main-employs .row {
        display: flex!important;
        gap: 0px;
    }.orker-form-label {
    font-size: 15px!important;
}
        }
   
</style>



   <div class="content-header">
        <h2>
              <i class="fas fa-edit"></i>Edit Profile
        </h2>
    </div>

<div class="orker-job-form-card main-content">
    <div class=" admin-profile-card">
        {{-- <div class="card-header">
            <h5>Edit Profile</h5>
        </div> --}}

        <div class="">

            {{-- SUCCESS MESSAGE --}}
            @if (Session::has('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ Session::get('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ERROR MESSAGE --}}
            @if (Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ Session::get('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div id="alertBox"></div>

            <form action="{{ route('employer.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf

                {{-- NAME & EMAIL --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                        <label class="orker-form-label">Name <span class="required-star">*</span></label>
                        <input type="text" name="name" id="name" class="form-input" 
                            value="{{ old('name', $user->name) }}"
                            oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-name"></span>
                       </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                        <label class="orker-form-label">Email <span class="required-star">*</span></label>
                        <input type="email" name="email" id="email" class="form-input" 
                            value="{{ old('email', $user->email) }}" required>
                        <span id="emailError" class="error-message"></span>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-email"></span>
                       </div>
                    </div>
                </div>

                {{-- PROFILE IMAGE & MOBILE --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                         <label class="orker-form-label">Profile Image (200 × 200)</label>
                        <input type="file" name="image" id="image" class="form-input" 
                            accept="image/jpeg,image/jpg,image/png">
                        <p class="allowed_type">Only JPG, JPEG, PNG allowed (200×200px, Max 2MB)</p>

                        <img src="{{ asset('employe_assets/images/' . ($user->image ?? 'user-dummy.png')) }}"
                            id="previewImage" class="preview-img" alt="profile">

                        @error('image')
                            <span class="error-message d-block mt-1">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-image"></span>
                       </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                         <label class="orker-form-label">Mobile Number <span class="required-star">*</span></label>
                        <input type="text" name="phone" class="form-input" 
                            value="{{ $user->phone }}" readonly disabled
                            style="background-color: #e9ecef; cursor: not-allowed;">
                        <small class="text-not-chnges">Mobile number cannot be changed</small>
                       </div>
                    </div>
                </div>

                {{-- COMPANY NAME --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                       <div class="orker-form-group">
                         <label class="orker-form-label">Company Name <span class="required-star">*</span></label>
                        <input type="text" name="company_name" id="company_name" class="form-input" 
                            value="{{ old('company_name', $user->employerDetail->company_name) }}"
                            placeholder="Enter company name" required>
                        @error('company_name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-company_name"></span>
                       </div>
                    </div>
                </div>

                {{-- ADDRESS & CITY --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                      <div class="orker-form-group">
                          <label class="orker-form-label">Address <span class="required-star">*</span></label>
                        <input type="text" name="address" id="address" class="form-input" 
                            value="{{ old('address', $user->employerDetail->address) }}"
                            placeholder="Enter complete address" required>
                        @error('address')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-address"></span>
                      </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                         <label class="orker-form-label">City <span class="required-star">*</span></label>
                        <input type="text" name="city" id="city" class="form-input" 
                            value="{{ old('city', $user->employerDetail->city) }}"
                            oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')"
                            placeholder="Enter city" required>
                        @error('city')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-city"></span>
                       </div>
                    </div>
                </div>

                {{-- STATE & PINCODE --}}
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                        <label class="orker-form-label">State <span class="required-star">*</span></label>
                        <select name="state" id="state" class="form-input" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->name }}" 
                                    {{ old('state', $user->employerDetail->state) == $state->name ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('state')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-state"></span>
                       </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="orker-form-group">
                        <label class="orker-form-label">Pin Code <span class="required-star">*</span></label>
                        <input type="text" name="pin_code" id="pin_code" class="form-input" 
                            value="{{ old('pin_code', $user->employerDetail->pin_code) }}"
                            maxlength="6" oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            placeholder="Enter pin code" required>
                        @error('pin_code')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-pin_code"></span>
                       </div>
                    </div>
                </div>

                {{-- IDENTITY DOCUMENT --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                       <div class="orker-form-group">
                          <label class="orker-form-label">Upload Identity Proof</label>
                        <input type="file" name="identity" id="identity" class="form-input" 
                            accept=".pdf,.doc,.docx">
                        <p class="allowed_type">
                            Aadhaar Card, PAN Card, Passport, Voter ID, Driving License (.pdf, .doc, .docx, Max 2MB)
                        </p>
                        <p class="file-info" id="identityFileName"></p>
                        
                        @if($user->employerDetail->identity_proof_path)
                            <div class="current-file">
                                <i class="fas fa-file-pdf"></i> Current Document: 
                                <a href="{{ asset('storage/' . $user->employerDetail->identity_proof_path) }}" target="_blank">
                                    View Document
                                </a>
                            </div>
                        @endif
                        
                        @error('identity')
                            <span class="error-message d-block mt-1">{{ $message }}</span>
                        @enderror
                        <span class="error-message" id="error-identity"></span>
                      </div>
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <div class="text-end ">
                    <button type="submit" class="btn orker-btn-submit" id="updateBtn">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                    <a href="{{ route('employer.dashboard') }}" class="btn orker-btn-submit btn-color">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const alertBox = $("#alertBox");

        // Image Preview
        document.querySelector('input[name="image"]').addEventListener('change', function () {
            const file = this.files[0];
            const img = document.getElementById("previewImage");

            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });

        // Identity file name display
        $("#identity").change(function() {
            const fileName = this.files[0]?.name || "";
            $("#identityFileName").text(fileName ? `Selected: ${fileName}` : "");
        });

        // Force alphabets only
        $("#name, #company_name, #city").on("input", function () {
            this.value = this.value.replace(/[^A-Za-z ]/g, "");
        });

        // Force numbers only
        $("#pin_code").on("input", function () {
            this.value = this.value.replace(/[^0-9]/g, "");
        });
      
    });
</script>
@endsection
