@extends('layouts/contentNavbarLayout')

@section('title', 'TPS - Master Data')
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
<script src="{{asset('js/pemilu/master-data/tps.js')}}"></script>
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


<!-- Data Table TPS -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-user-voice bx-sm me-sm-2"></i>TPS Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddTps">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add TPS</span></span>
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
          <th>Kode</th>
          <th>TPS</th>
          <th>Alamat</th>
          <th>Provinsi</th>
          <th>Kabupaten</th>
          <th>Daerah</th>
          <th>Desa</th>
          <th>Saksi</th>
          <th>Suara Caleg</th>
          <th>Suara Partai</th>
          <th>Docs</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table TPS -->

<hr class="my-5">

<!-- Modal Add TPS -->
<div class="modal fade" id="modalAddTps" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddTps" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>TPS Form<span></h3>
            <p>Add new TPS.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="tps_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                <label for="addKode" class="form-label">Kode <span style='color:red'>*</span></label>
                <input type="text" id="addKode" name="tps_code" class="form-control" placeholder="Enter Kode" >
              </div>
              <div class="col mb-3">
                <label for="addTps" class="form-label">TPS <span style='color:red'>*</span></label>
                <input type="text" id="addTps" name="tps_name" class="form-control" placeholder="Enter TPS" >
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                  <label for="addAddress" class="form-label">Alamat <span style='color:red'>*</span></label>
                  <textarea id="addAddress" name="tps_address" class="form-control" placeholder="Explanation about the new address" rows="5" style="max-height: 100px;resize: none;"></textarea>
                </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="addProvince" name="tps_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="addRegency" name="tps_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">Daerah Pemilihan</label>
                <select id="addDistrict" name="tps_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Desa</label>
                <select id="addVillage" name="tps_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label" for="role_id">Saksi <span style='color:red'>*</span></label>
                    <select id="addRoleName" name="role_id" class="ac_role form-select" @required(true)>
                        <option value="">Select Saksi</option>
                    </select>
                </div>
            </div>
            <div class="row g2">
                <div class="col mb-3">
                    <label for="addSuaraCaleg" class="form-label">Suara Caleg <span style='color:red'>*</span></label>
                    <input type="text" id="addSuaraCaleg" name="tps_suara_caleg" class="form-control" placeholder="Enter Suara Caleg" >
                </div>
                <div class="col mb-3">
                    <label for="addSuaraPartai" class="form-label">Suara Partai <span style='color:red'>*</span></label>
                    <input type="text" id="addSuaraPartai" name="tps_suara_partai" class="form-control" placeholder="Enter Suara Partai" >
                </div>
            </div>
            <div class="row g2">
              <div class="col mb-3">
                <label class="form-label" for="bs-validation-upload-file">Dosc</label>
                <input type="file" class="form-control" name="tps_docs" id="bs-validation-upload-file"/>
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
<!--/ Modal Add TPS -->

<!-- Modal Edit TPS -->
<div class="modal fade" id="modalEditTps" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditTps" data-method="add">
        <div class="modal-body">
          <div id="editFormLabel" class="text-center mb-4">
            <h3><span>TPS Form<span></h3>
            <p>Add edit TPS.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="editStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="editStatus" name="tps_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
                <label for="editKode" class="form-label">Kode <span style='color:red'>*</span></label>
                <input type="text" id="editKode" name="tps_code" class="form-control" placeholder="Enter Code" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="editTps" class="form-label">TPS <span style='color:red'>*</span></label>
                <input type="text" id="editTps" name="tps_name" class="form-control" placeholder="Enter TPS" @required(true)>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                  <label for="editAddress" class="form-label">Alamat</label>
                  <textarea id="editAddress" name="tps_address" class="form-control" placeholder="Explanation about the new address" rows="5" style="max-height: 100px;resize: none;"></textarea>
                </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <input type="hidden" id="oldImage" name="oldImage" value="">
                <label class="form-label">Current Docs</label>
                <div class="col-md-3">
                    <img src="#" class="rounded current-photo" style="max-width: 100%; height: auto;">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="tps_docs">Change Docs</label>
                <input type="file" class="form-control" id="tps_docs" name="tps_docs"/>
                <br>
                <div id="imagePreview"></div>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editProvince">Provinsi <span style='color:red'>*</span></label>
                <select id="editProvince" name="tps_province" class="form-select" @required(true)>
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editRegency">Kabupaten <span style='color:red'>*</span></label>
                <select id="editRegency" name="tps_regency" class="form-select" @required(true)>
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row g-2">
              <div class="col mb-3">
                <label class="form-label" for="editDistrict">Daerah</label>
                <select id="editDistrict" name="tps_district" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="editVillage">Desa</label>
                <select id="editVillage" name="tps_village" class="form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
                <div class="col mb-3">
                    <label class="form-label" for="role_id">Saksi <span style='color:red'>*</span></label>
                    <select id="editRoleName" name="role_id" class="ac_edit_role form-select" @required(true)>
                        <option value="">Select Saksi</option>
                    </select>
                </div>
            </div>
            <div class="row g2">
                <div class="col mb-3">
                    <label for="editSuaraCaleg" class="form-label">Suara Caleg <span style='color:red'>*</span></label>
                    <input type="text" id="addSuaraCaleg" name="tps_suara_caleg" class="form-control" placeholder="Enter Suara Caleg" >
                </div>
                <div class="col mb-3">
                    <label for="editSuaraPartai" class="form-label">Suara Partai <span style='color:red'>*</span></label>
                    <input type="text" id="addSuaraPartai" name="tps_suara_partai" class="form-control" placeholder="Enter Suara Partai" >
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
<!--/ Modal Edit Tps -->
@endsection