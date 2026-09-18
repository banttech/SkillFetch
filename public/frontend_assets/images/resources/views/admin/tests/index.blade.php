@extends('layout.admin.app')

@section('content')
<link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">

<div class="content-card qualification-adds">
    <div class="div-main-haeds">
        <h2 class="page-title">Manage Tests</h2>
        <div>
            <a href="{{ route('admin.test.create') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Add Test
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
        <form method="GET" action="{{ route('admin.test.index') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by test name or fee..."
                           value="{{ request('search') }}">
                </div>

                {{-- Per Page --}}
                {{-- <div class="col-md-2">
                    <label class="form-label">Per Page</label>
                    <select name="per_page" class="form-select">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div> --}}

                {{-- Action Buttons --}}
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.test.index') }}" class="btn btn-secondary">
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
                            Test Name
                            @if(request('sort_by') === 'name')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'fees', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Fee
                            @if(request('sort_by') === 'fees')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'timing', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Duration
                            @if(request('sort_by') === 'timing')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Total Questions</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_marks', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Total Marks
                            @if(request('sort_by') === 'total_marks')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Passing Marks</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Created Date
                            @if(request('sort_by') === 'created_at')
                                <i class="fas fa-sort-{{ request('sort_order') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tests as $test)
                    <tr>
                        <td>{{ $test->name }}</td>
                        <td>₹{{ number_format($test->fees, 2) }}</td>
                        <td>{{ $test->timing }} min</td>
                        <td>{{ $test->total_question }}</td>
                        <td>{{ $test->total_marks}}</td>
                        <td>{{ $test->passing_marks }}</td>
                        <td>{{ $test->created_at->format('d-M-Y') }}</td>
                        <td>
                            @if($test->status === 'active')
                                <span class="text-success">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.test.edit', $test->id) }}" class="btn-edits">
                                Edit
                            </a>
                            {{-- <form action="{{ route('admin.test.destroy', $test->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this test?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" style="background: #dc3545; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer;">
                                    Delete
                                </button>
                            </form> --}}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No tests found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $tests->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit on select change
    document.querySelector('select[name="per_page"]')?.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

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