@extends('layouts/contentNavbarLayout')

@section('title', 'Partner - Master Data')
@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/formvalidation/dist/css/formValidation.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/block-ui/block-ui.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('js/site-visit/master-data/partner.js')}}"></script>
@endsection

@section('content')
<style>
  #imagePreview {
      width: 150px;
      height: 150px;
      background-position: center center;
      background-size: cover;
      -webkit-box-shadow: 0 0 1px 1px rgba(0, 0, 0, .3);
      display: inline-block;
  }
</style>


<!-- Data Table Partner -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>Partner Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddPartner">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Partner</span></span>
      </button>
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
          <th>No</th>
          <th>Foto</th>
          <th>Name</th>
          <th>Adress</th>
          <th>Total Visit</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Partner -->

<hr class="my-5">

<!-- Modal Add Partner -->
<div class="modal fade" id="modalAddPartner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddPartner" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Partner Form<span></h3>
            <p>Add new Partner.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="partner_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
                    <span class="switch-toggle-slider">
                      <span class="switch-on">
                        <i class="bx bx-check"></i>
                      </span>
                      <span class="switch-off">
                        <i class="bx bx-x"></i>
                      </span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label for="addName" class="form-label">Name <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="partner_name" class="form-control" placeholder="Enter Name" >
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label for="addNik" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="addNik" name="partner_nik" class="form-control" placeholder="Nomor Induk Karyawan" >
              </div>
              <div class="col mb-3">
                <label for="addEmail" class="form-label">Email <span style='color:red'>*</span></label>
                <input type="email" id="addEmail" name="partner_email" class="form-control" placeholder="Enter Email"  >
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label for="addPhone" class="form-label">Mobile Phone <span style='color:red'>*</span></label>
                <input type="text" id="addPhone" name="partner_phone" class="form-control" placeholder="Enter Phone"  >
              </div>
              <div class="col mb-3">
                <label for="addPostalCode" class="form-label">Postal Code</label>
                <input type="text" id="addPostalCode" name="partner_postal_code" class="form-control" placeholder="Enter Postal Code">
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addAddress" class="form-label">Address</label>
                <textarea id="addAddress" name="partner_address" class="form-control" placeholder="Enter detail address" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Province <span style='color:red'>*</span></label>
                <select id="addProvince" name="partner_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Regency <span style='color:red'>*</span></label>
                <select id="addRegency" name="partner_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">District</label>
                <select id="addDistrict" name="partner_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Village</label>
                <select id="addVillage" name="partner_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="bs-validation-upload-file">Photo</label>
                <input type="file" class="form-control" name="partner_photo" id="bs-validation-upload-file"/>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="partner_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Modal Add Partner -->

<!-- Modal Edit Partner -->
<div class="modal fade" id="modalEditPartner" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditPartner" data-method="add">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>Partner Form<span></h3>
            <p>Add edit Partner.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="editStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="editStatus" name="partner_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
                    <span class="switch-toggle-slider">
                      <span class="switch-on">
                        <i class="bx bx-check"></i>
                      </span>
                      <span class="switch-off">
                        <i class="bx bx-x"></i>
                      </span>
                    </span>
                  </label>
                </div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="editCode" class="form-label">Code <span style='color:red'>*</span></label>
                <input type="text" id="editCode" name="partner_code" class="form-control" placeholder="Enter Code" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="editName" class="form-label">Name <span style='color:red'>*</span></label>
                <input type="text" id="editName" name="partner_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="editNIK" class="form-label">NIK <span style='color:red'>*</span></label>
                <input type="text" id="editNIK" name="partner_nik" class="form-control" placeholder="Enter NIK" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="editEmail" class="form-label">Email <span style='color:red'>*</span></label>
                <input type="email" id="editEmail" name="partner_email" class="form-control" placeholder="Enter Email" @required(true) >
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <input type="hidden" id="oldImage" name="oldImage" value="">
                <label class="form-label">Current Photo</label>
                <div class="col-md-3">
                    <img src="#" class="rounded current-photo" style="max-width: 100%; height: auto;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="partner_photo">Change Photo</label>
                <input type="file" class="form-control" id="partner_photo" name="partner_photo"/>
                <br>
                <div id="imagePreview"></div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="editAddress" class="form-label">Address</label>
                <textarea id="editAddress" name="partner_address" class="form-control" placeholder="Enter detail address" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label for="editPostalCode" class="form-label">Postal Code</label>
                <input type="text" id="editPostalCode" name="partner_postal_code" class="form-control" placeholder="Enter Postal Code">
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editProvince">Province <span style='color:red'>*</span></label>
                <select id="editProvince" name="partner_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editRegency">Regency <span style='color:red'>*</span></label>
                <select id="editRegency" name="partner_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editDistrict">District</label>
                <select id="editDistrict" name="partner_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="editVillage">Village</label>
                <select id="editVillage" name="partner_village" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label for="editPhone" class="form-label">Phone</label>
                <input type="text" id="editPhone" name="partner_phone" class="form-control" placeholder="Enter Phone">
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="editDesc" class="form-label">Description</label>
                <textarea id="editDesc" name="partner_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ Modal Edit Partner -->
@endsection