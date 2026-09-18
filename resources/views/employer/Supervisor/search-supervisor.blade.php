@extends('layout.employer.app')

@section('title', $page_title ?? 'Search Supervisor')

@section('content')



    <div class="job-details-card">
        <h1 class="job-details-title">Search Supessrvisors</h1>

        {{-- =================== SEARCH FORM =================== --}}
        <form id="searchForm" method="GET" action="{{ route('employer.search.supervisor') }}" class="form-logins">

            <div class="div-fomr-only">

                <div class="form-row-custom">

                    {{-- Name --}}
                    <div class="form-group-custom">
                        <input type="text" name="name" class="form-input" value="{{ request('name') }}"
                            placeholder="Enter name">
                    </div>

                    {{-- Preferred Work Location --}}
                    <div class="form-group-custom">
                        <input type="text" name="preferred_location" class="form-input"
                            value="{{ request('preferred_location') }}" placeholder="Preferred Work Location">
                    </div>

                </div>

                <div class="form-row-custom">

                    {{-- Own Bike --}}
                    <div class="form-group-custom">
                        <select name="own_bike" class="form-input">
                            <option value="">Own Bike?</option>
                            <option value="1" {{ request('own_bike') == "1" ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ request('own_bike') == "0" ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    {{-- Skills --}}
                    <div class="form-group-custom">
                        <input type="text" name="skill_tag" class="form-input" value="{{ request('skill_tag') }}"
                            placeholder="Search by skill">
                    </div>

                </div>

                <div class="form-row-custom">

                    {{-- City (TEXT INPUT or static dropdown) --}}
                    <div class="form-group-custom">
                        <input type="text" name="city" class="form-input" value="{{ request('city') }}"
                            placeholder="Enter city">
                    </div>

                    <div class="form-group-custom new-form-dis">
                        <button class="search-btn-2" type="submit">Search</button>
                    </div>

                </div>

            </div>

        </form>

        <br>

        {{-- =================== SUPERVISOR TABLE =================== --}}
        <div class="table-responsive">
            <table class="table supervisors-table">
                <thead>
                    <tr>
                        <th>Supervisor Name</th>
                        <th>City</th>
                        <th>Preferred Work Location</th>
                        <th>Own a Bike</th>
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
                                @forelse($sup->workLocations as $loc)
                                    <span class="skill-badge">{{ $loc->name }}</span>
                                @empty
                                    <span class="text-muted">N/A</span>
                                @endforelse
                            </td>

                            <td>
                                @if($sup->own_bike)
                                    <span class="status-badge yes">Yes</span>
                                @else
                                    <span class="status-badge no">No</span>
                                @endif
                            </td>

                            <td>
                                @forelse($sup->skills as $skill)
                                    <span class="skill-badge">{{ $skill->name }}</span>
                                @empty
                                    <span class="text-muted">No Skills</span>
                                @endforelse
                            </td>

                            <td class="view-profiles">
                                <a href="{{ route('employer.supervisor.profile', $sup->id) }}"
                                    class="view-profile-btn">View&nbsp;Profile</a>

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-danger">No supervisors found</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

    </div>

@endsection