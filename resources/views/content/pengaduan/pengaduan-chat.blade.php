@extends('layouts/contentNavbarLayout')

@section('title', 'Pengaduan - Create')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<!-- Include jQuery UI Autocomplete JS -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Include jQuery UI Autocomplete CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('page-script')
<script src="{{asset('js/pengaduan/pengaduan-chat.js')}}"></script>
@endsection

@section('content')
<style>
    /* CSS style for the Pengaduan Number */
    #pengaduan_number {
      background-color: #e7e7ff;
    }

</style>
<h4 class="fw-bold py-3 mb-4">
  <span class="text-muted fw-light">Forms/</span>
  Pengaduan
</h4>
<!-- Pengaduan -->
<div class="row">
  <div class="col-12">
    <div class="card">
      <form id="formAddPengaduan" data-method="add">
        <div class="card-header sticky-element bg-label-secondary d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
          <h5 class="card-title mb-sm-0 me-2">Pengaduan Bar</h5>
          <div class="action-btns">
            <a href="/pengaduan/pengaduan-manage" class="btn btn-label-secondary me-3"><span class="align-middle"> Cancel</span></a>
            <button type="submit" id="pengaduan_submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <!-- Pengaduan -->
              <h5 class="mb-4">Pengaduan</h5>
              <hr>
              <!-- Note -->
              <h5 class="my-4">Uraian Pengaduan anda <strong>{{ session('user_uniq_name') }}</strong></h5>
              <div class="col-md-12">
                <label class="form-label" for="pengaduan_number">Pengaduan Number <span style='color:red'>*</span></label>
                <input type="text" id="pengaduan_number" name="pengaduan_number" class="form-control" placeholder="Pengaduan Number" value="{{ $code_number }}" @required(true) @readonly(true)>
              </div>
              <div class="row gy-3">
                <div class="col-md-12">
                  <label class="form-label" for="pengaduan_note">Note <span style='color:red'>*</span></label>
                  <textarea id="autosize-note" rows="5" name="pengaduan_note" class="form-control note-maxlength" maxlength="500"></textarea>
                </div>
              </div>
              <div class="pt-4">
                <button type="submit" class="btn btn-primary me-sm-3 me-1" id="pengaduan_submit">Submit</button>
                <a href="/pengaduan/pengaduan-manage" class="btn btn-label-secondary">Cancel</a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- /Visit Order -->

@endsection
