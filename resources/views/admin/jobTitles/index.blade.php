@extends('layout.admin.app')

@section('content')
<link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">

<div class="content-card qualification-adds">
    <div class="div-main-haeds">
        <h2 class="page-title">Job Titles</h2>
        <div>
            <a href="{{ route('admin.job-titles.create') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Add Job Title
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.job-titles.index') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search job titles..."
                           value="{{ request('search') }}">
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.job-titles.index') }}" class="btn btn-secondary">
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
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Job Title
                            @if(request('sort_by') === 'title')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        Created By
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Created Date
                            @if(request('sort_by') === 'created_at')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($jobTitles as $jt)
                    <tr>
                        <td>{{ $jt->title }}</td>
                        <td>{{ ucfirst($jt->created_by) }}</td>
                        <td>{{ $jt->created_at->format('d-M-Y') }}</td>
                        <td>
                            <a href="{{ route('admin.job-titles.edit', $jt->id) }}" class="btn-edits">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            No job titles found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $jobTitles->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Debounce search input
    let searchTimeout;
    document.querySelector('input[name="search"]')?.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
</script>
@endpush

@endsection
