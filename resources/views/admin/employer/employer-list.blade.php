@extends('layout.admin.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
<div class="content-card   employer-main-list">

    <div class="div-main-haeds">
        <h2 class="page-title">Employers List</h2>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.employer.employerList') }}" id="filterForm">
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

                {{-- Date From --}}
                <div class="col-md-3">
                    <label class="form-label">Registered From</label>
                    <input type="date" 
                           name="date_from" 
                           class="form-control" 
                           value="{{ request('date_from') }}">
                </div>

                {{-- Date To --}}
                <div class="col-md-3">
                    <label class="form-label">Registered To</label>
                    <input type="date" 
                           name="date_to" 
                           class="form-control" 
                           value="{{ request('date_to') }}">
                </div>

                {{-- Per Page --}}
                {{-- <div class="col-md-2">
                    <label class="form-label">Show</label>
                    <select name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div> --}}

                {{-- Action Buttons --}}
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2" title="Filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.employer.employerList') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-container ">
        <table class="supervisor-table">
            <thead>
                <tr>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Emp ID
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
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Registered At
                            @if(request('sort_by') === 'created_at')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        Action
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($employers as $emp)
                <tr>
                    <td><b>Emp-{{ $emp->id }}</b></td>
                    <td>{{ ucfirst($emp->user->name) }}</td>
                    <td>{{ $emp->user->email }}</td>
                    <td>{{ $emp->user->phone }}</td>
                    <td>{{ $emp->created_at->format('d-m-Y') }}</td>
                    <td><a href="{{route('admin.employerPostedJob',$emp->id)}}" class="btn-view">View Posted Jobs <span class="{{ count($emp->jobs) == 0 ? 'text-danger' : 'text-success' }}">({{count($emp->jobs)}})</span></a></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-danger" style="padding:40px;">
                        No employers found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $employers->links() }}
        </div>
    </div>

</div>

@endsection