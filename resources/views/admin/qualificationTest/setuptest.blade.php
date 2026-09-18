@extends('layout.admin.app')

@section('content')
  <link rel="stylesheet" href="{{ asset('admin_assets/css/filter.css') }}">
<div class="content-card qualification-adds">
    <div class="div-main-haeds">
        <h2 class="page-title">Setup Q & A</h2>
        <div>
            <a href="{{ route('admin.qualification.add-question') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i> Add Question
            </a>
        </div>
    </div>

    @if (session('error'))x
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
   
    <div class="filter-section" >
        <form method="GET" action="{{ route('admin.qualification.setuptest') }}" id="filterForm">
            <div class="row g-3">
                {{-- Search --}}
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search questions or answers..."
                           value="{{ request('search') }}">
                </div>

                {{-- Department Filter --}}
                <div class="col-md-3">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" 
                                    {{ request('department') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Answer Type Filter --}}
                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="answer_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="text" {{ request('answer_type') == 'text' ? 'selected' : '' }}>
                            Descriptive
                        </option>
                        <option value="single" {{ request('answer_type') == 'single' ? 'selected' : '' }}>
                            Single Select
                        </option>
                        <option value="multi" {{ request('answer_type') == 'multi' ? 'selected' : '' }}>
                            Multi Select
                        </option>
                        <option value="image" {{ request('answer_type') == 'image' ? 'selected' : '' }}>
                            Image
                        </option>
                        <option value="video" {{ request('answer_type') == 'video' ? 'selected' : '' }}>
                            Video
                        </option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.qualification.setuptest') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>

           
        </form>
    </div>

    
    <div class="table-container">

        <div class="row m-3">
            <a href="{{ route('admin.qualification.bulk-import') }}" class="btn btn-post-job">
                <i class="fas fa-plus"></i>Import Questions
            </a>
        </div>

        
        <table class="supervisor-table">
            <thead>
                <tr>
                    <th>
                      Question
                    </th>
                    <th>Type</th>
                    <th>Answer</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>
                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'ASC' ? 'DESC' : 'ASC']) }}">
                            Date
                            @if(request('sort_by') === 'created_at' || !request('sort_by'))
                                <i class="fas fa-sort-{{ request('sort_order', 'DESC') === 'ASC' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($questions as $q)
                    <tr>
                        <td>{{ Str::limit($q->question, 50) }}</td>

                        <td>
                            @switch($q->answer_type)
                                @case('text') Descriptive @break
                                @case('single') Single Select @break
                                @case('multi') Multi Select @break
                                @case('image') Image @break
                                @case('video') Video @break
                                @default -
                            @endswitch
                        </td>

                        <td>
                            @if($q->answers->count())
                                {{ Str::limit($q->answers->pluck('answer')->implode(', '), 40) }}
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>{{ $q->department->name ?? 'N/A' }}</td>

                        <td>
                            @if($q->status)
                                <span class="status-verified">Active</span>
                            @else
                                <span class="text-danger">Inactive</span>
                            @endif
                        </td>

                        <td>{{ $q->created_at->format('d-M-Y') }}</td>

                        <td>
                            <a href="{{ route('admin.qualification.edit-question', $q->id) }}" class="btn-edits">
                                Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No questions found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $questions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-submit on select change for better UX
    document.querySelectorAll('select[name="department"], select[name="answer_type"], select[name="status"], select[name="per_page"]').forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
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