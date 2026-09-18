@extends('layout.admin.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
    <style>
        .verified {
            background-color: #bbffbb;
        }
        .rejected {
            background-color: #ffbbbb;
        }
        .underReview {
            background-color: #ffffc8;
        }
        .underReviewTd {
            color: #af8300 !important;
            font-weight: 600;
            font-size: 17px;
        }
    </style>

    <div class="content-card">

        <div class="div-main-haeds">
            <h2 class="page-title">Supervisors List</h2>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter Section --}}
        <div class="filter-section">
            <form method="GET" action="{{ route('admin.supervisor.supervisors-List') }}" id="filterForm">
                <div class="row g-3">
                    {{-- Search --}}
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Search by name, email or phone..."
                               value="{{ request('search') }}">
                    </div>

                    {{-- Verification Status Filter --}}
                    <div class="col-md-3">
                        <label class="form-label">Verification Status</label>
                        <select name="verification_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pass" {{ request('verification_status') == 'pass' ? 'selected' : '' }}>
                                Verified (Pass)
                            </option>
                            <option value="fail" {{ request('verification_status') == 'fail' ? 'selected' : '' }}>
                                Rejected (Fail)
                            </option>
                            <option value="underReview" {{ request('verification_status') == 'underReview' ? 'selected' : '' }}>
                                Under Review
                            </option>
                            <option value="not_attempted" {{ request('verification_status') == 'not_attempted' ? 'selected' : '' }}>
                                Not Attempted
                            </option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" 
                               name="date_from" 
                               class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" 
                               name="date_to" 
                               class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>

                  

                    {{-- Action Buttons --}}
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i>
                        </button>
                        <a href="{{ route('admin.supervisor.supervisors-List') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

      

        <div class="table-container">
            <table class="supervisor-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                                Sup ID
                                @if(request('sort_by') === 'id' || !request('sort_by'))
                                    <i class="fas fa-sort-{{ request('sort_order', 'DESC') === 'ASC' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                                Name
                                @if(request('sort_by') === 'name')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'email', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                                Email
                                @if(request('sort_by') === 'email')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Mobile No.</th>
                        <th>Profile Verified</th>
                        <th>
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                                Registered At
                                @if(request('sort_by') === 'created_at')
                                    <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                        <th>Qualification Test Result</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($supervisors as $sup)
                        <tr
                            class="{{ $sup->latestTestAttempt
                                ? ($sup->latestTestAttempt->status === 'pass'
                                    ? 'verified'
                                    : ($sup->latestTestAttempt->status === 'fail'
                                        ? 'rejected'
                                        : ($sup->latestTestAttempt->status === 'underReview'
                                        ? 'underReview'
                                        : '')))
                                : '' }}">
                            <td>Sup-{{ $sup->id }}</td>
                            <td>{{ ucfirst($sup->user->name) }}</td>
                            <td>{{ $sup->user->email }}</td>
                            <td>{{ $sup->user->phone }}</td>

                            <td>
                                @if ($sup->latestTestAttempt && $sup->latestTestAttempt->status == 'pass')
                                    <span class="status-verified">Yes</span>
                                @elseif($sup->latestTestAttempt && $sup->latestTestAttempt->status == 'fail')
                                    <span class="status-not-verified">Rejected</span>    
                                @elseif($sup->latestTestAttempt && $sup->latestTestAttempt->status == 'underReview')
                                    <span class="underReviewTd">Under Review</span>
                                @else
                                    <span class="status-not-verified">No</span>    
                                @endif
                            </td>

                            <td>{{ $sup->created_at->format('d M Y') }}</td>

                            <td>
                                <a href="{{ route('admin.test-review.index', $sup->id) }}" class="btn-view">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted" style="padding:40px;">
                                No supervisors found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $supervisors->links() }}
            </div>
        </div>
    </div>
@endsection