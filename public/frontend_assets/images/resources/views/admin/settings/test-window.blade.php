@extends('layout.admin.app')

@section('content')
   
    
    <div class="content-card add-quests">
        <div class="div-main-haeds">
            <h2 class="page-title">Set Test Reattempt Period</h2>
        </div>

         @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

     @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
       
    <form action="{{ route('admin.settings.test-window.update') }}" method="POST"  class="add-ques">
        @csrf
        @method('PUT')

  <div class="row g-3">
         <div class="col-12 col-md-12 top-2">
                    <label>Reattempt Period (In Hours) <span class="text-danger">*</span></label>
                  <input
                type="number"
                name="hours"
                value="{{ old('hours', $hours) }}"
                class="custom-input form-control"
                placeholder="e.g. 24"
                min="1"
                max="168"
                step="1"
                  oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            >
                    @error('hours')
                <span class="text-danger">{{ $message }}</span>
            @enderror
                </div>

</div>
       <div class="mt-3">
        <button type="submit" class="btn btn-submit-form">
             Save
        </button>
        </div>
    </form>
    </div>
@endsection