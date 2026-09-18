@extends('layout.admin.app')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    
    <div class="content-card add-quests">
        <div class="div-main-haeds">
            <h2 class="page-title">Add Department</h2>
        </div>

        <form class="add-ques" action="{{ route('admin.departments.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Department Name --}}
                <div class="col-12 col-md-12 top-2">
                    <label>Department Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           placeholder="Enter department name"
                           class="custom-input form-control"
                           value="{{ old('name') }}"
                           required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

             
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-submit-form">
                    Add Department
                </button>
              
            </div>
        </form>
    </div>
@endsection