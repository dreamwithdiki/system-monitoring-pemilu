@extends('layouts/contentNavbarLayout')

@section('title', 'Pengaduan - Manage')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/shepherd/shepherd.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bootstrap-maxlength/bootstrap-maxlength.js')}}"></script>
<script src="{{asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>
<script src="{{asset('assets/vendor/libs/autosize/autosize.js')}}"></script>
<script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/shepherd/shepherd.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
<script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('assets/js/wizard-ex-create-deal.js')}}"></script>
<script src="{{asset('js/pengaduan/pengaduan-manage.js')}}"></script>
@endsection

@section('content')
<style>
  /* CSS style for the Pengaduan */
  #pengaduan_number {
    background-color: #e7e7ff;
  }
    .btn-refresh {
      border: 1px solid transparent;
      border-radius: 7px;
    }
</style>
<!-- Data Table Pengaduan -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-list-ol bx-sm me-sm-2"></i>Pengaduan Table</h5>
    </div>
  </div>
  <div class="card-datatable text-nowrap">
    <div class="d-flex justify-content-end pe-3">
      <div style="width: 25%">
        <input type="text" id="search" name="search" class="form-control search mt-3" placeholder="Search">
      </div>
    </div>
    <table class="datatables-ajax table table-hover">
      <thead>
        <tr>
          <th style="width: 2%;">No</th>
          <th style="width: 5%;">Pengaduan Number</th>
          <th style="width: 10%;">Uraian Pengaduan</th>
          <th style="width: 10%;">Nama</th>
          <th style="width: 10;">Created By</th>
          <th style="width: 10;">Jawaban</th>
          <th style="width: 3%;">Status</th>
          <th style="width: 1%;">Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Pengaduan -->

<hr class="my-5">

<!-- Modal Detail Pengaduan -->
<div class="modal fade" id="modalDetailPengaduan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Default Icons Wizard -->
        <div class="col-12">
          <div class="bs-stepper wizard-icons wizard-icons-example mt-2">

            <div class="bs-stepper-header">
              <div class="step" data-target="#detail">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-detail"></i>
                  </span>
                  <span class="bs-stepper-label">Detail</span>
                </button>
              </div>
              <div class="line"></div>
              <div class="step" data-target="#history">
                <button type="button" class="step-trigger">
                  <span class="bs-stepper-circle">
                    <i class="bx bx-history"></i>
                  </span>
                  <span class="bs-stepper-label">History </span>
                </button>
              </div>
            </div>
            <div class="bs-stepper-content">

              <!-- Detail -->
              <div id="detail" class="content">
                <form id="formPengaduan" enctype="multipart/form-data">
                  <div id="detFormLabel" class="text-center mb-4">
                    <h3><span>Detail Pengaduan<span></h3>
                    <p>detail.</p>
                  </div>
                  <div class="">
                    <div class="row">
                        <div class="col mb-3">
                          <label class="form-label" for="det_number">Number</label>
                          <h6 id="det_number"></h6>
                        </div>
                    </div>
                    <div class="row g-2">
                      <div class="col mb-3">
                        <label for="det_note" class="form-label">Pengaduan</label>
                        <h6 id="det_note"></h6>
                      </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                          <label class="form-label" for="det_answer">Jawaban</label>
                          <h6 id="det_answer"></h6>
                        </div>
                    </div>
                  </div>
                </form>
              </div>
              <!-- Detail -->

              <!-- History -->
              <div id="history" class="content">
                <div id="editFormLabel" class="text-center mb-4">
                  <h3><span>History Pengaduan<span></h3>
                  <p>history.</p>
                </div>
                <div class="">
                  <ul class="timeline" id="history_pengaduan">
                    
                  </ul>
                </div>
              </div>
              <!-- End History -->
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
<!--/ Modal Detail Pengaduan -->

<!-- Modal Edit Pengaduan -->
<div class="modal fade" id="modalEditPengaduan" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditPengaduan" data-method="edit">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Edit Pengaduan<span></h3>
            <p>edit.</p>
          </div>
          <div class="m-4">
            <div class="card-body">
              <div class="row">
                
                  <div class="row" id="editUraian">
                    <input type="hidden" id="pengaduan_status" name="pengaduan_status" class="form-control">
                    <input type="hidden" name="role_id">
                    <!-- Note -->
                    <h5 class="my-4">Edit Uraian Pengaduan anda <strong>{{ session('user_uniq_name') }}</strong></h5>

                    <div class="col-md-12">
                      <label class="form-label" for="pengaduan_number">Pengaduan Number <span style='color:red'>*</span></label>
                      <input type="text" id="pengaduan_number" name="pengaduan_number" class="form-control" placeholder="Pengaduan Number" @required(true) @readonly(true)>
                    </div>

                    @if(session('role_id') == 1)
                        <!-- Tampilan untuk role_id 1 -->
                        <div class="col-md-12">
                            <label class="form-label" for="pengaduan_answer">Jawaban <span style='color:red'>*</span></label>
                            <textarea id="autosize-answer" rows="5" name="pengaduan_answer" class="form-control answer-maxlength" maxlength="1000" placeholder="Answer"></textarea>
                        </div>
                    @else
                        <!-- Tampilan untuk role_id 2, 3, 4 -->
                        <div class="col-md-12">
                            <label class="form-label" for="pengaduan_note">Note</label>
                            <textarea id="autosize-note" rows="5" name="pengaduan_note" class="form-control note-maxlength" maxlength="1000" placeholder="Tuliskan Pengaduan"></textarea>
                        </div>
                    @endif

                  </div>
                </div>
                <div class="modal-footer">
                  <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
</div>
<!--/ Modal Edit Pengaduan -->
@endsection
