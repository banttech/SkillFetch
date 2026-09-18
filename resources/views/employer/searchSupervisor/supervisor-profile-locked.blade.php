@extends('layout.employer.app')

@section('content')
<div class="content-header">
        <h2>
           <i class="fas fa-user-circle"></i>
         Supervisor Locked Profile
        </h2>
    </div>
<div class="job-details-card">
    {{-- <div class="locked-header">
        <h1 class="job-details-title">
            <i class="fas fa-lock locked-icon"></i> Supervisor Profile - Locked
        </h1>
        <div class="unlock-price-badge">
            <i class="fas fa-rupee-sign"></i> {{ number_format(100, 2) }}
        </div>
    </div> --}}

    <div class="locked-info-alert">
        <i class="fas fa-info-circle"></i> 
        <span>This profile is locked. Pay <strong>₹100</strong> to view full contact details of this Supervisor.</span>
    </div>

    {{-- Limited Preview Card --}}
     <div class="supervisor-profile-card">
        
        {{-- Basic Preview --}}
       <div class="preview-section">
            <div class="profile-basic-header">
               <div class="profile-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="preview-basic-info">
                     <h3 class="supervisor-name">{{ $supervisor->user->name }}</h3>
                      <p class="supervisor-location">
                        <i class="fas fa-map-marker-alt"></i> {{ $supervisor->city }}, {{ $supervisor->state->name ?? 'N/A' }}
                    </p>
                </div>
            </div>

              <hr class="section-divider">

            {{-- Assets --}}
           <div class="assets-grid">
                <div class="asset-item">
                    <i class="fas fa-motorcycle"></i>
                    <span>Own a Bike:</span>
                    @if($supervisor->own_bike)
                         <span class="badge-yes-preview">Yes</span>
                    @else
                          <span class="badge-no-preview">No</span>
                    @endif
                </div>
                  <div class="asset-item">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Own a Phone:</span>
                    @if($supervisor->own_phone)
                        <span class="badge-yes-preview">Yes</span>
                    @else
                        <span class="badge-no-preview">No</span>
                    @endif
                </div>
            </div>

           <hr class="section-divider">

            {{-- Skills Preview --}}
             <div class="job-description">
                 <p class="job-description-label"> Skills:
                </p>
                <div class="">
                    @forelse($supervisor->skills as $skill)
                           <span class="skill-badge">{{ $skill->name }}</span>
                        @empty
                            <span class="text-muted">No skills added</span>
                        @endforelse
                </div>
            </div>


              <div class="job-description">
                <p class="job-description-label">Experience Areas:
                </p>
                <div class="">
                     @forelse($supervisor->experiences as $exp)
                            <span class="skill-badge">{{ $exp->name }}</span>
                        @empty
                            <span class="text-muted">No experience added</span>
                        @endforelse
                </div>
            </div>

            {{-- Locations Preview --}}
             <div class="job-description">
                 <p class="job-description-label"> Preferred Locations:
                </p>
                <div class="preview-badges">
                    @foreach($supervisor->workLocations as $loc)
                        <span class="skill-badge">{{ $loc->name }}</span>
                    @endforeach
                 
                </div>
            </div>

          <hr class="preview-divider">

            {{-- Blurred Contact Info --}}
            <div class="blurred-section">
                <div class="blur-overlay">
                    <i class="fas fa-lock blur-lock-icon"></i>
                    <p class="blur-text">Contact details locked</p>
                </div>
                <div class="blurred-content">
                    <p><strong>Email:</strong> supervisor@example.com</p>
                    <p><strong>Phone:</strong> +91 98765 43210</p>
                    <p><strong>Full Address:</strong> Complete address, landmark, pincode details will be visible after payment</p>
                </div>
            </div>
                 <hr class="preview-divider">
        </div>

    </div>

    {{-- Payment Section --}}
    <div class="payment-action-section">
        <div class="payment-benefits">
            <h4 class="benefits-title">
              <i class="fas fa-unlock"></i>  Unlock to View:
            </h4>
            <ul class="benefits-list">
                <li><i class="fas fa-check"></i> Full contact details (Email & Phone)</li>
                <li><i class="fas fa-check"></i> Complete address with pincode</li>
                <li><i class="fas fa-check"></i> Test scores and qualifications</li>
            </ul>
        </div>

        @include('components.razorpay-payment-button', [
            'buttonId' => 'payProfileViewBtn_' . $supervisor->id,
            'buttonText' => 'Pay ₹100 to Unlock Full Profile',
            'initiateRoute' => route('employer.supervisor.profile-view-payment'),
            'redirectUrl' => route('employer.supervisor.profile', $supervisor->id),
            'paymentName' => 'Profile View Fee',
            'requestBody' => ['supervisor_id' => $supervisor->id],
        ])
    </div>

 <div class="text-right mt-4">
        <a href="{{ route('employer.search.supervisor') }}" class="back-button-locked">
            <i class="fas fa-arrow-left"></i> Back to Search
        </a>
    </div>

</div>

@endsection
