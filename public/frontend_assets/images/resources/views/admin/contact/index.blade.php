@extends('layout.admin.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
<div class="content-card employer-main-list">

    <div class="div-main-haeds">
        <h2 class="page-title">Contacts List</h2>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.contacts') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by name, email or phone..."
                           value="{{ request('search') }}">
                </div>

                {{-- Date From --}}
                <div class="col-md-3">
                    <label class="form-label">Submitted From</label>
                    <input type="date" 
                           name="date_from" 
                           class="form-control" 
                           value="{{ request('date_from') }}">
                </div>

                {{-- Date To --}}
                <div class="col-md-3">
                    <label class="form-label">Submitted To</label>
                    <input type="date" 
                           name="date_to" 
                           class="form-control" 
                           value="{{ request('date_to') }}">
                </div>

             
                {{-- Action Buttons --}}
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2" title="Filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.contacts') }}" class="btn btn-secondary" title="Reset">
                        <i class="fas fa-redo"></i> Reset
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
                            Submitted At
                            @if(request('sort_by') === 'created_at')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    {{-- <th>
                        Action
                    </th> --}}
                </tr>
            </thead>

            <tbody>
                @forelse ($contacts as $contact)
                <tr>
                  
                    <td>{{ ucfirst($contact->name) }}</td>
                    <td>{{ $contact->email }}</td>
                    <td>{{ $contact->phone }}</td>
                    <td>{{ $contact->created_at->format('d-m-Y') }}</td>
                  
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted" style="padding:40px;">
                        No employers found matching your filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $contacts->links() }}
        </div>
    </div>

</div>

@endsection