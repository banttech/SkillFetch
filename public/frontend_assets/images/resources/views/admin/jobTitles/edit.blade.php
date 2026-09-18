


@extends('layout.admin.app')

@section('content')

 <div class="content-card add-quests">
    <div class="div-main-haeds">
        <h2 class="page-title">Edit Job Title</h2>
    </div>

    <form action="{{ route('admin.job-titles.update', $jobTitle->id) }}" method="POST" class="add-ques">
        @csrf
        @method('put')
        <div class="row g-3">
           <div class="col-12 col-md-12 top-2">
                <label>Job Title Name<span class="text-danger">*</span></label>
                <input type="text" 
                       name="title" 
                       id="title" 
                       class="custom-input form-control"
                        value="{{ old('title', $jobTitle->title) }}" 
                       placeholder="Enter job title" 
                       required>
               @error('title')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

         <div class="mt-3">
            <button type="submit" class="btn btn-submit-form">
              <i class="fas fa-save"></i> Update Job Title
            </button>
        </div>
    </form>
</div>

@endsection

