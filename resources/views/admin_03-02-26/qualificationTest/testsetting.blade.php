@extends('layout.admin.app')

@section('content')
    <div class="content-card">

        <div class="div-main-haeds">
            <h2 class="page-title">Test Settings</h2>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
         @if (session('error'))
            <div class="alert alert-error">
                {{ session('error') }}
            </div>
        @endif

        <form class="add-ques" action="{{ route('admin.test-settings.store') }}" method="POST">
            @csrf


            <div class="row g-3">

                <div class="col-12 col-md-6 top-2">
                    <label>Test Fee <span class="text-danger">*</span></label>
                    <input type="number" name="fees" value="{{ $setting->fees ?? '' }}"
                        class="custom-input form-control" placeholder="Enter test fee">
                    @error('fees')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Test Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ $setting->name ?? '' }}"
                        class="custom-input form-control" placeholder="Enter test name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Enter per Question Mark <span class="text-danger">*</span></label>
                    <input type="number" id="per_mark" name="marks" value="{{ $setting->marks ?? '' }}"
                        class="custom-input form-control" placeholder="Enter per question marks" min="1" max="1">
                    @error('marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Test Timing <span class="text-danger">*</span></label>
                    <select name="timing" class="custom-input form-control">
                        <option value="">Select Time</option>

                        @foreach ([60, 75, 90] as $t)
                            <option value="{{ $t }}"
                                {{ isset($setting) && $setting->timing == $t ? 'selected' : '' }}>
                                {{ $t }} Minutes
                            </option>
                        @endforeach
                    </select>
                    @error('timing')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Questions per Department <span class="text-danger">*</span></label>
                    <input type="number" id="q_per_dept" name="question_per_department"
                        value="{{ $setting->question_per_department ?? '' }}" class="custom-input form-control"
                        placeholder="Enter no. of questions per department">
                    @error('question_per_department')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>No. of Departments <span class="text-danger">*</span></label>
                    <input type="number" id="no_of_departments" name="no_of_departments"
                        value="{{ $setting->no_of_departments ?? $departmentCount }}" class="custom-input form-control"
                        placeholder="No. of Departments" readonly>
                    @error('no_of_departments')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Total No. of Questions <span class="text-danger">*</span></label>
                    <input type="number" id="total_questions" name="total_question"
                        value="{{ $setting->total_question ?? '' }}" class="custom-input form-control" readonly>
                    @error('total_question')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-6 top-2">
                    <label>Total Marks <span class="text-danger">*</span></label>
                    <input type="number" id="total_marks" name="total_marks" value="{{ $setting->total_marks ?? '' }}"
                        class="custom-input form-control" readonly>
                    @error('total_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-12 col-md-12 top-2">
                    <label>Passing Marks <span class="text-danger">*</span></label>
                    <input type="number" name="passing_marks" value="{{ $setting->passing_marks ?? '' }}"
                        class="custom-input form-control" placeholder="Enter passing marks">
                    @error('passing_marks')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <button type="submit" class="btn btn-submit-form">Save</button>
        </form>

    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qPerDeptEl = document.getElementById('q_per_dept');
            const deptEl = document.getElementById('no_of_departments');
            const perMarkEl = document.getElementById('per_mark');
            const totalQEl = document.getElementById('total_questions');
            const totalMEl = document.getElementById('total_marks');

            if (!qPerDeptEl || !deptEl || !perMarkEl || !totalQEl || !totalMEl) {
                // elements missing — bail out quietly
                return;
            }

            function toNumber(v) {
                // convert to integer, treat empty/invalid as 0
                const n = parseInt(v, 10);
                return Number.isFinite(n) ? n : 0;
            }

            function calculateTotals() {
                const qPerDept = toNumber(qPerDeptEl.value);
                const dept = toNumber(deptEl.value);
                const perMark = toNumber(perMarkEl.value);

                const totalQ = qPerDept * dept;
                const totalM = totalQ * perMark;

                totalQEl.value = totalQ;
                totalMEl.value = totalM;
            }

            // run once on load (in case values were prefilled)
            calculateTotals();

            // react to changes
            qPerDeptEl.addEventListener('input', calculateTotals);
            deptEl.addEventListener('input', calculateTotals);
            perMarkEl.addEventListener('input', calculateTotals);
        });

       
    </script>
@endsection
