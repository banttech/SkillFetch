@extends('layout.employer.app')

@section('content')
<style>
    .form-group-custom.new-form-dis {
    text-align: end;
    display: block;
}
   
</style>
<div class="content-header">
        <h2>
           <i class="fas fa-users"></i>
          Search Supervisors
        </h2>
</div>
<div class="job-details-card">
    {{-- <h1 class="job-details-title">Search Supervisors</h1> --}}

    {{-- =================== SEARCH FORM =================== --}}
    <form id="searchForm" method="GET" action="{{ route('employer.search.supervisor') }}" class="form-logins">

        <div class="div-fomr-only">

            <div class="form-row-custom">
                {{-- Name --}}
                <div class="form-group-custom">
                    <input type="text" name="name" class="form-input" value="{{ request('name') }}"
                        placeholder="Enter name">
                </div>

                {{-- City --}}
                <div class="form-group-custom">
                    <input type="text" name="city" class="form-input" value="{{ request('city') }}"
                        placeholder="Enter city">
                </div>
            </div>

            <div class="form-row-custom">
                {{-- Preferred Work Location --}}
                <div class="form-group-custom">
                    <select name="preferred_location" class="form-input">
                        <option value="">Search by preferred work location</option>
                        @forelse ($workLocations as $workLocation)
                            <option value="{{$workLocation->id}}" {{ request('preferred_location') == $workLocation->id ? 'selected' : '' }}>
                                {{ucfirst($workLocation->name)}}
                            </option>
                        @empty
                            <option value="">No location found.</option>
                        @endforelse
                    </select>
                </div>

                {{-- Skills --}}
                <div class="form-group-custom">
                    <select name="skill_tag" class="form-input">
                        <option value="">Search by skill</option>
                        @forelse ($skills as $skill)
                            <option value="{{$skill->id}}" {{ request('skill_tag') == $skill->id ? 'selected' : '' }}>
                                {{ucfirst($skill->name)}}
                            </option>
                        @empty
                            <option value="">No skill found.</option>
                        @endforelse
                    </select>
                </div>
            </div>

            <div class="form-row-custom">
                {{-- Own Bike --}}
                <div class="form-group-custom">
                    <select name="own_bike" class="form-input">
                        <option value="">Own a Bike?</option>
                        <option value="1" {{ request('own_bike') == "1" ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('own_bike') == "0" ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                {{-- Own Phone --}}
                <div class="form-group-custom">
                    <select name="own_phone" class="form-input">
                        <option value="">Own a Phone?</option>
                        <option value="1" {{ request('own_phone') == "1" ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ request('own_phone') == "0" ? 'selected' : '' }}>No</option>
                    </select>
                </div>

               
            </div>
  <div class="row form-group-custom new-form-dis text-end">
                   <div class="col-md-12">
                     <button class="search-btn-2" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                   </div>
                </div>
        </div>

    </form>

    <br>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"></h4>
        <a href="{{ route('employer.search.supervisor.export', request()->all()) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Export Unlocked Profiles
        </a>
    </div>

    {{-- =================== SUPERVISOR TABLE =================== --}}
    <div class="table-responsive">
        <table class="table supervisors-table">
            <thead>
                <tr>
                    <th>Supervisor Name</th>
                    <th>City</th>
                    <th>Preferred Work Location</th>
                    <th>Own a Bike</th>
                    <th>Own a Phone</th>
                    <th>Skills</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($supervisors as $sup)
                    <tr>
                        <td><b>{{ $sup->user->name }}</b></td>

                        <td>{{ $sup->city }}</td>

                        <td>
                            @forelse($sup->workLocations->take(2) as $loc)
                                <span class="skill-badge">{{ $loc->name }}</span>
                            @empty
                                <span class="text-muted">N/A</span>
                            @endforelse
                            @if($sup->workLocations->count() > 2)
                                <span class="text-muted">+{{ $sup->workLocations->count() - 2 }}</span>
                            @endif
                        </td>

                        <td>
                            @if($sup->own_bike)
                                <span class="status-badge yes">Yes</span>
                            @else
                                <span class="badge-no">No</span>
                            @endif
                        </td>

                        <td>
                            @if($sup->own_phone)
                                <span class="status-badge yes">Yes</span>
                            @else
                                <span class="badge-no">No</span>
                            @endif
                        </td>

                        <td>
                            @forelse($sup->skills->take(2) as $skill)
                                <span class="skill-badge">{{ $skill->name }}</span>
                            @empty
                                <span class="text-muted">No Skills</span>
                            @endforelse
                            @if($sup->skills->count() > 2)
                                <span class="text-muted">+{{ $sup->skills->count() - 2 }}</span>
                            @endif
                        </td>

                        <td class="view-profiles">
                            @if($sup->canViewFree)
                                {{-- Free Access --}}
                                <a href="{{ route('employer.supervisor.profile', $sup->id) }}"
                                    class="view-profile-btn">
                                    <i class="fas fa-eye"></i> View Profile
                                </a>
                                @if($sup->hasApplied)
                                    <small class="applied-badge-small">
                                        <i class="fas fa-check-circle"></i> Applied to your job
                                    </small>
                                @elseif($sup->hasPaid)   
                                  <small class="applied-badge-small">
                                        <i class="fas fa-check-circle"></i> Profile unlocked via payment
                                    </small> 
                                @endif
                            @else
                                {{-- Need to Pay --}}
                                <a href="{{ route('employer.supervisor.profile', $sup->id) }}"
                                    class="view-profile-btn locked-btn" style="color:white">
                                    <i class="fas fa-lock"></i> View Profile (₹100)
                                </a>
                                  <small class="applied-badge-small" style="color: #c73403">
                                        <i class="fas fa-check-circle"></i> Pay (₹100) to unlock this profile
                                    </small>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="text-center text-danger">No supervisors found</td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $supervisors->links() }}
    </div>

</div>

<style>
    .applied-badge-small {
        display: block;
        margin-top: 8px;
        color: #28a745;
        font-size: 12px;
        font-weight: 600;
    }

    .locked-btn {
        background: red !important;
        color: white !important;
    }




</style>

@endsection