@extends('layout.admin.app')

@section('content')

<div class="content-card qualification-adds">
    <div class="div-main-haeds">
        <h2 class="page-title">Add Job Title</h2>
        {{-- <div>
            <a href="{{ route('admin.job-titles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div> --}}
    </div>

    <form action="{{ route('admin.job-titles.store') }}" method="POST" class="add-ques">
        @csrf
        <div class="row">
           <div class="col-12 col-md-12 top-2">
                <label>Job Title Name<span class="text-danger">*</span></label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="custom-input form-control"
                       value="{{ old('title') }}" 
                       placeholder="Enter job title" 
                       required>
                @error('title')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

         <div class="mt-3">
            <button type="submit" class="btn btn-submit-form">
                <i class="fas fa-save"></i> Save Job Title
            </button>
        </div>
    </form>
</div>

@endsection
