@extends('layouts/contentNavbarLayout')

@section('title', 'Site - Master Data')
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
<script src="{{asset('js/site-visit/master-data/site.js')}}"></script>
@endsection

@section('content')
<!-- Data Table Site -->
<div class="card">
  <div class="card-header d-flex justify-content-between">
    <div class="head-label">
      <h5 class="card-title mb-0 pt-2"><span><i class="tf-icons bx bx-current-location bx-sm me-sm-2"></i>Site Table</h5>
    </div>
    <div class="text-end pt-3 pt-md-0">
      <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#modalAddSite">
        <span><i class="tf-icons bx bx-plus-medical me-sm-2"></i> <span class="d-none d-sm-inline-block">Add Site</span></span>
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
          <th style="width: 3%;">No</th>
          <th style="width: 7%;">Site Code</th>
          <th style="width: 15%;">Site Name</th>
          <th style="width: 7%;">Client</th>
          <th style="width: 15%;">Address</th>
          <th style="width: 5%;">Status</th>
          <th style="width: 2%;">Actions</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
<!--/ Data Table Site -->

<hr class="my-5">

<!-- Modal Add Site -->
<div class="modal fade" id="modalAddSite" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content modal-block-loader">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddSite" data-method="add">
        <div class="modal-body">
          <div id="addFormLabel" class="text-center mb-4">
            <h3><span>Site Form<span></h3>
            <p>Add new site.</p>
          </div>
          <div class="m-4">
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addStatus">Status <span style='color:red'>*</span></label>
                <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                  <label class="switch switch-primary">
                    <input id="addStatus" name="site_status" class="form-check-input switch-input me-2" type="checkbox" style="width: 2.4rem;" @checked(true)>
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
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="client_id">Client Name <span style='color:red'>*</span></label>
                <select id="addClient" name="client_id" class="ac_client form-select" @required(true)>
                  <option value="">Select Client Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label for="addCode" class="form-label">Site Code <span style='color:red'>*</span></label>
                <input type="text" id="addCode" name="site_code" class="form-control" placeholder="Enter Code" @required(true)>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addName" class="form-label">Site Name <span style='color:red'>*</span></label>
                <input type="text" id="addName" name="site_name" class="form-control" placeholder="Enter Name" @required(true)>
              </div>
              <div class="col mb-3">
                <label for="addPostalCode" class="form-label">Postal Code <span style='color:red'>*</span></label>
                <input type="text" id="addPostalCode" name="site_postal_code" class="form-control" placeholder="Enter Postal Code">
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addAddress" class="form-label">Address <span style='color:red'>*</span></label>
                <textarea id="addAddress" name="site_address" class="form-control" placeholder="Enter detail address" rows="5" style="max-height: 100px;resize: none;"></textarea>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addFax" class="form-label">Fax</label>
                <input type="text" id="addFax" name="site_fax" class="form-control" placeholder="Enter Fax">
              </div>
              <div class="col mb-3">
                <label for="addPhone" class="form-label">Phone</label>
                <input type="text" id="addPhone" name="site_phone" class="form-control" placeholder="Enter Phone">
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addProvince">Province</label>
                <select id="addProvince" name="site_province" class="ac_province form-select">
                  <option value="">Select Province Name</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addRegency">Regency</label>
                <select id="addRegency" name="site_regency" class="ac_regency form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label class="form-label" for="addDistrict">District</label>
                <select id="addDistrict" name="site_district" class="ac_district form-select">
                  <option value="">Choice</option>
                </select>
              </div>
              <div class="col mb-3">
                <label class="form-label" for="addVillage">Village</label>
                <select id="addVillage" name="site_village" class="ac_village form-select">
                  <option value="">Choice</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="col mb-3">
                <label for="addDesc" class="form-label">Description</label>
                <textarea id="addDesc" name="site_desc" class="form-control" placeholder="Explanation about the new desc" rows="5" style="max-height: 100px;resize: none;"></textarea>
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
<!--/ Modal Add Site -->

<!-- Modal manage contact -->
<div class="modal fade" id="modalManageContact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div>
          <form id="formAddContact" data-method="add">
            <div id="addFormContactlabel" class="text-center mb-4">
              <h3>Contact Form</h3>
              <p>Add new contact data.</p>
            </div>
            <div class="m-4">
              <div class="row">
                <div class="col mb-3">
                  <label class="form-label" for="addContactStatus">Status</label>
                  <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                    <label class="switch switch-primary">
                      <input id="addContactStatus" name="site_contact_status" class="form-check-input switch-input me-2" type="checkbox"style="width: 2.4rem;" @checked(true)>
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
                  <label for="addContactFullname" class="form-label">Fullname <span style='color:red'>*</span></label>
                  <input type="text" id="addContactFullname" name="site_contact_fullname" class="form-control" placeholder="Enter Fullname" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="addContactEmail" class="form-label">Email</label>
                  <input type="email" id="addContactEmail" name="site_contact_email" class="form-control" placeholder="Enter Email" @required(true)>
                </div>
              </div>
              <div class="row g-2">
                <div class="col mb-3">
                  <label for="addContactMobilePhone" class="form-label">Mobile Phone</label>
                  <input type="text" id="addContactMobilePhone" name="site_contact_mobile_phone" class="form-control" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="addContactPhone" class="form-label">Phone</label>
                  <input type="text" id="addContactPhone" name="site_contact_phone" class="form-control" @required(true)>
                </div>
              </div>
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Add Contact</button>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
              </div>
            </div>
          </form>
        </div>

        <hr>

        <!-- Data table contact -->
        <div class="card-datatable text-nowrap">
          <table class="datatables-ajax-contact table table-hover">
            <thead>
              <tr>
                <th>No</th>
                <th>Fullname</th>
                <th>Email</th>
                <th>Mobile Phone</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!--/ end Modal manage contact -->

<!-- Modal edit contact -->
<div class="modal fade" id="modalEditContact" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formEditContact" data-method="edit">
        <div class="modal-body">
          <div>
            <div id="editFormContactLabel" class="text-center mb-4">
              <h3>Contact Form</h3>
              <p>Edit contact.</p>
            </div>
            <div class="m-4">
              <div class="row">
                <div class="col mb-3">
                  <label class="form-label" for="editContactStatus">Status</label>
                  <div class="row" style="display: flex; flex-direction: column; align-items: flex-start;">
                    <label class="switch switch-primary">
                      <input id="editContactStatus" name="site_contact_status" class="form-check-input switch-input me-2" type="checkbox"style="width: 2.4rem;" @checked(true)>
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
                  <label for="editContactFullname" class="form-label">Fullname <span style='color:red'>*</span></label>
                  <input type="text" id="editContactFullname" name="site_contact_fullname" class="form-control" placeholder="Enter Fullname" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="editContactEmail" class="form-label">Email</label>
                  <input type="email" id="editContactEmail" name="site_contact_email" class="form-control" placeholder="Enter Email" @required(true)>
                </div>
              </div>
              <div class="row g-2">
                <div class="col mb-3">
                  <label for="editContactMobilePhone" class="form-label">Mobile Phone</label>
                  <input type="text" id="editContactMobilePhone" name="site_contact_mobile_phone" class="form-control" @required(true)>
                </div>
                <div class="col mb-3">
                  <label for="editContactPhone" class="form-label">Phone</label>
                  <input type="text" id="editContactPhone" name="site_contact_phone" class="form-control" @required(true)>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update Contact</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!--/ end Modal edit contact -->

<script>
  // untuk karakter hanya angka.
  function hanyaAngka(evt) {
      var charCode = (evt.which) ? evt.which : event.keyCode
      if (charCode > 31 && (charCode < 48 || charCode > 57))

          return false;
      return true;
  }
</script>
@endsection