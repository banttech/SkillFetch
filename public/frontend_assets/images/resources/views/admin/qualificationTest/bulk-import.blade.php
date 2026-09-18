@extends('layout.admin.app')

@section('content')

<style>
.top-2 {
    margin-bottom: 1rem;
    gap: 5px 0;
}
.text-orange {
    color: #ff9800 !important;
}
.text-danger {
    width: 100%;
}

/* ── Drop zone ── */
.bulk-dropzone {
    border: 2.5px dashed #b0bec5;
    border-radius: 10px;
    padding: 2.2rem 1rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s ease;
    background: #f8f9fb;
}
.bulk-dropzone:hover,
.bulk-dropzone.dragover {
    border-color: #1a73e8;
    background: #e8f0fe;
}
.bulk-dropzone .dz-icon   { font-size: 2.5rem; margin-bottom: .5rem; }
.bulk-dropzone .dz-title  { font-size: .98rem; color: #37474f; }
.bulk-dropzone .dz-title strong { color: #1a73e8; }
.bulk-dropzone .dz-hint   { font-size: .8rem; color: #90a4ae; margin-top: .25rem; }
#file-badge { display:none; margin-top:.7rem; }
#file-badge span {
    display: inline-flex; align-items: center; gap:.4rem;
    background:#e3f2fd; border:1px solid #90caf9; border-radius:20px;
    padding:.25rem .9rem; font-size:.88rem; font-weight:600; color:#1565c0;
}

/* ── Progress ── */
#import-progress { display:none; margin-top:.8rem; }
.imp-prog-wrap { height:8px; border-radius:20px; background:#e0e0e0; overflow:hidden; }
.imp-prog-bar  { height:100%; width:0%; background:linear-gradient(90deg,#1a73e8,#0d47a1); border-radius:20px; transition:width .3s; }

/* ── Result banners ── */
.imp-banner {
    border-radius: 8px;
    padding: 1rem 1.2rem;
    display: flex;
    align-items: flex-start;
    gap: .8rem;
    margin-bottom: .9rem;
}
.imp-banner.success { background:#e8f5e9; border-left:4px solid #43a047; }
.imp-banner.warning { background:#fff8e1; border-left:4px solid #fb8c00; }
.imp-banner.danger  { background:#fce4ec; border-left:4px solid #e53935; }
.imp-banner .bn-icon { font-size:1.45rem; line-height:1; }
.imp-banner .bn-body h6 { margin:0 0 .2rem; font-weight:700; font-size:.95rem; }
.imp-banner .bn-body p  { margin:0; font-size:.87rem; color:#546e7a; }

/* ── Column guide ── */
.col-guide { width:100%; border-collapse:collapse; font-size:.87rem; }
.col-guide th { background:#1F3864; color:#fff; padding:.5rem .8rem; text-align:left; font-weight:600; }
.col-guide td { padding:.45rem .8rem; border-bottom:1px solid #eef0f5; vertical-align:top; }
.col-guide tr:last-child td { border-bottom:none; }
.col-guide tr:nth-child(even) td { background:#f8f9fb; }
.col-guide code { background:#eef2ff; border-radius:4px; padding:1px 6px; font-size:.82rem; color:#1F3864; }
.badge-req  { background:#fde8e8; color:#c62828; border-radius:4px; padding:1px 7px; font-size:.74rem; font-weight:700; }
.badge-cond { background:#fff8e1; color:#e65100; border-radius:4px; padding:1px 7px; font-size:.74rem; font-weight:700; }
</style>

<div class="content-card add-quests">

    <div class="div-main-haeds">
        <h2 class="page-title">Bulk Import Questions</h2>
    </div>

    {{-- ═══ RESULT BANNERS ═══ --}}
    @if(session('error'))
        <div class="imp-banner danger">
            <span class="bn-icon">❌</span>
            <div class="bn-body">
                <h6>Import Failed</h6>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if(session()->has('import_success'))
        @php
            $successCount = session('import_success');
            $failedCount  = session('import_failed');
            $failedFile   = session('failed_file');
        @endphp

        @if($successCount > 0)
            <div class="imp-banner success">
                <span class="bn-icon">✅</span>
                <div class="bn-body">
                    <h6>Import Completed</h6>
                    <p><strong>{{ $successCount }}</strong> question(s) with answers imported successfully.</p>
                </div>
            </div>
        @endif

        @if($failedCount > 0)
            <div class="imp-banner warning">
                <span class="bn-icon">⚠️</span>
                <div class="bn-body">
                    <h6>{{ $failedCount }} Row(s) Skipped</h6>
                    <p>These rows failed validation. Download the report, fix the issues, then re-import only those rows.</p>
                    @if($failedFile)
                        <a href="{{ route('admin.qualification.bulk-import.download') }}?file={{ urlencode($failedFile) }}"
                           class="btn btn-submit-form mt-2 d-inline-flex align-items-center gap-2"
                           style="background:#e53935;border-color:#e53935;font-size:.85rem;padding:.4rem 1rem;">
                            ⬇ Download Failed Rows Report
                        </a>
                    @endif
                </div>
            </div>
        @endif

        @if($successCount === 0 && $failedCount === 0)
            <div class="imp-banner warning">
                <span class="bn-icon">ℹ️</span>
                <div class="bn-body">
                    <h6>No Data Found</h6>
                    <p>The uploaded file had no data rows. Please check the file and try again.</p>
                </div>
            </div>
        @endif
    @endif

    {{-- ═══ FORM ═══ --}}
    <form id="importForm" method="POST"
          action="{{ route('admin.qualification.bulk-import.process') }}"
          enctype="multipart/form-data"
          class="add-ques">
        @csrf

        {{-- File upload --}}
        <div class="row g-3">
            <div class="col-12 top-2">
                <label>Upload Excel File <span class="text-danger">*</span></label>

                <div class="bulk-dropzone" id="dropZone"
                     onclick="document.getElementById('import_file').click()">
                    <div class="dz-icon">📊</div>
                    <div class="dz-title">
                        <strong>Click to browse</strong> or drag &amp; drop your Excel file here
                    </div>
                    <div class="dz-hint">Accepted: .xlsx, .xls &nbsp;|&nbsp; Max size: 10 MB</div>
                    <div id="file-badge">
                        <span><span>📎</span><span id="badge-text"></span></span>
                    </div>
                </div>

                <input type="file" name="import_file" id="import_file"
                       accept=".xlsx,.xls" style="display:none;">

                @error('import_file')
                    <span class="text-danger">{{ $message }}</span>
                @enderror

                {{-- <small class="text-orange">
                    For <strong>image / video</strong> questions provide a public URL in the <strong>media_url</strong> column — the file is downloaded and stored automatically.
                </small> --}}
            </div>
        </div>

        {{-- Progress bar --}}
        <div id="import-progress">
            <small class="text-orange">Uploading and processing, please wait…</small>
            <div class="imp-prog-wrap mt-1">
                <div class="imp-prog-bar" id="prog-bar"></div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="row g-3 m-2">
            <div class="col-12 d-flex flex-wrap gap-2 align-items-center top-2">

                <button type="submit" class="btn btn-submit-form m-2" id="importBtn" disabled>
                    <span id="btn-label">📥 Import Questions</span>
                    <span id="btn-spin" style="display:none;">
                        <span class="spinner-border spinner-border-sm" role="status"></span> Processing…
                    </span>
                </button>

                <a href="{{ route('admin.qualification.bulk-import.download') }}?file=sample"
                   class="btn btn-submit-form"
                   style="background:#2e7d32;border-color:#2e7d32;">
                    ⬇ Download Sample Template
                </a>

                {{-- <a href="{{ route('admin.qualification.setuptest') }}"
                   class="btn btn-submit-form"
                   style="background:#546e7a;border-color:#546e7a;">
                    ← Back to Questions
                </a> --}}

            </div>
        </div>
    </form>

    {{-- ═══ COLUMN GUIDE ═══ --}}
    {{-- <div class="row mt-4">
        <div class="col-12">
            <label style="font-weight:700;color:#1F3864;font-size:.97rem;margin-bottom:.6rem;display:block;">
                📋 Column Format Guide
            </label>
            <div style="overflow-x:auto;">
                <table class="col-guide">
                    <thead>
                        <tr>
                            <th style="width:150px;">Column</th>
                            <th style="width:100px;">Required?</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>question</code></td>
                            <td><span class="badge-req">Required</span></td>
                            <td>Question text. Max 255 characters.</td>
                        </tr>
                        <tr>
                            <td><code>department_id</code></td>
                            <td><span class="badge-req">Required</span></td>
                            <td>
                                Numeric ID of an existing department.
                                <br>
                                <small style="color:#90a4ae;">
                                    Available:
                                    @foreach($departments as $d)
                                        <strong>{{ $d->id }}</strong> = {{ $d->name }}@if(!$loop->last), @endif
                                    @endforeach
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td><code>answer_type</code></td>
                            <td><span class="badge-req">Required</span></td>
                            <td>
                                <code>text</code> | <code>single</code> | <code>multi</code> | <code>image</code> | <code>video</code>
                            </td>
                        </tr>
                        <tr>
                            <td><code>sub_answer_type</code></td>
                            <td><span class="badge-cond">Conditional</span></td>
                            <td>Required when <code>answer_type</code> is <code>image</code> or <code>video</code>. Values: <code>text</code> | <code>single</code> | <code>multi</code>.</td>
                        </tr>
                        <tr>
                            <td><code>status</code></td>
                            <td><span class="badge-req">Required</span></td>
                            <td><code>1</code> = Active &nbsp;|&nbsp; <code>0</code> = Inactive</td>
                        </tr>
                        <tr>
                            <td><code>media_url</code></td>
                            <td><span class="badge-cond">Conditional</span></td>
                            <td>
                                Required when <code>answer_type</code> is <code>image</code> or <code>video</code>. Must be a publicly accessible URL.<br>
                                <small class="text-orange">Image: jpeg/png/jpg/gif/webp ≤ 5 MB &nbsp;|&nbsp; Video: mp4/mov/webm ≤ 50 MB</small>
                            </td>
                        </tr>
                        <tr>
                            <td><code>text_answer</code></td>
                            <td><span class="badge-cond">Conditional</span></td>
                            <td>Required when effective answer type is <code>text</code>. Max 500 characters.</td>
                        </tr>
                        <tr>
                            <td><code>option_1</code> … <code>option_4</code></td>
                            <td><span class="badge-cond">Conditional</span></td>
                            <td>Required for <code>single</code> / <code>multi</code>. All 4 options must be filled.</td>
                        </tr>
                        <tr>
                            <td><code>correct_options</code></td>
                            <td><span class="badge-cond">Conditional</span></td>
                            <td>
                                Required for <code>single</code> / <code>multi</code>.<br>
                                <strong>Single:</strong> exactly one number, e.g. <code>2</code><br>
                                <strong>Multi:</strong> comma-separated min 2, e.g. <code>1,3</code>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <small class="text-orange mt-2 d-block">
                Rows that fail any validation are <strong>skipped</strong> and never partially saved.
                All skipped rows with reasons are included in the downloadable error report.
            </small>
        </div>
    </div> --}}

</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function () {
    const $input = $('#import_file');
    const $btn   = $('#importBtn');
    const $drop  = $('#dropZone');

    // ── File selected ──
    $input.on('change', function () {
        const file = this.files[0];
        if (file) {
            $('#badge-text').text(file.name + '  (' + (file.size / 1024).toFixed(1) + ' KB)');
            $('#file-badge').show();
            $btn.prop('disabled', false);
        } else {
            $('#file-badge').hide();
            $btn.prop('disabled', true);
        }
    });

    // ── Drag & drop ──
    $drop.on('dragover dragenter', function (e) {
        e.preventDefault(); e.stopPropagation();
        $(this).addClass('dragover');
    }).on('dragleave dragend', function () {
        $(this).removeClass('dragover');
    }).on('drop', function (e) {
        e.preventDefault(); e.stopPropagation();
        $(this).removeClass('dragover');
        const files = e.originalEvent.dataTransfer.files;
        if (!files.length) return;
        const ext = files[0].name.split('.').pop().toLowerCase();
        if (!['xlsx', 'xls'].includes(ext)) {
            alert('Only .xlsx or .xls files are accepted.');
            return;
        }
        const dt = new DataTransfer();
        dt.items.add(files[0]);
        $input[0].files = dt.files;
        $input.trigger('change');
    });

    // ── Submit ──
    $('#importForm').on('submit', function () {
        if (!$input[0].files.length) return false;
        $btn.prop('disabled', true);
        $('#btn-label').hide();
        $('#btn-spin').show();
        $('#import-progress').show();
        let pct = 0;
        setInterval(function () {
            pct = Math.min(pct + Math.random() * 12, 88);
            $('#prog-bar').css('width', pct + '%');
        }, 350);
    });
});
</script>
@endsection


