@php
$configData = Helper::appClasses();
@endphp

@push('pricing-script')
<script src="{{asset('assets/js/pages-pricing.js')}}"></script>
@endpush

<!-- Pricing Modal -->
<div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-pricing">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <!-- Pricing Plans -->
        <div class="rounded-top">
          <h2 class="text-center mb-2 mt-0 mt-md-4">Find the right plan for your site</h2>
          <p class="text-center pb-3"> Get started with us - it's perfect for individuals and teams. Choose a subscription plan that meets your needs. </p>
          <div class="d-flex align-items-center justify-content-center flex-wrap gap-2 py-5 mb-0 mb-md-4">
            <label class="switch switch-primary ms-sm-5 ps-sm-5 me-0">
              <span class="switch-label">Monthly</span>
              <input type="checkbox" class="switch-input price-duration-toggler" checked />
              <span class="switch-toggle-slider">
                <span class="switch-on"></span>
                <span class="switch-off"></span>
              </span>
              <span class="switch-label">Annual</span>
            </label>
            <div class="mt-n5 ms-n5 ml-2 mb-2 d-none d-sm-inline-flex align-items-start">
              <img src="{{asset('assets/img/pages/pricing-arrow-'.$configData['style'].'.png')}}" alt="arrow img" class="scaleX-n1-rtl" data-app-dark-img="pages/pricing-arrow-dark.png" data-app-light-img="pages/pricing-arrow-light.png">
              <span class="badge badge-sm bg-label-primary">Save upto 10%</span>
            </div>
          </div>

          <div class="row mx-0 gy-3">
            <!-- Basic -->
            <div class="col-lg mb-md-0 mb-4">
              <div class="card border rounded shadow-none">
                <div class="card-body position-relative">
                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/icons/unicons/bookmark.png')}}" alt="Starter Image" height="80">
                  </div>
                  <h3 class="card-title fw-semibold text-center text-capitalize mb-1">Basic</h3>
                  <p class="text-center">A simple start for everyone</p>
                  <div class="text-center mb-5">
                    <div class="d-flex justify-content-center mb-1">
                      <sup class="h5 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                      <h1 class="fw-bold h1 mb-0 text-primary">0</h1>
                      <sub class="h5 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                    </div>
                    <small class="position-absolute start-0 end-0 m-auto price-yearly price-yearly-toggle text-muted">Free</small>
                  </div>

                  <ul class="ps-3 my-4 list-unstyled">
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> 100 responses a month</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Unlimited forms and surveys</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Unlimited fields</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Basic form creation tools</li>
                    <li class="mb-0"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Up to 2 subdomains</li>
                  </ul>

                  <a href="{{url('auth/register-basic')}}" class="btn btn-label-success d-grid w-100">Your Current Plan</a>
                </div>
              </div>
            </div>

            <!-- Pro -->
            <div class="col-lg mb-md-0 mb-4">
              <div class="card border-primary border shadow-none">
                <div class="card-body position-relative">
                  <div class="position-absolute end-0 me-4 top-0 mt-4">
                    <span class="badge bg-label-primary">Popular</span>
                  </div>
                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/icons/unicons/wallet-round.png')}}" alt="Pro Image" height="80">
                  </div>
                  <h3 class="card-title fw-semibold text-center text-capitalize mb-1">Pro</h3>
                  <p class="text-center">For small to medium businesses</p>
                  <div class="text-center mb-5">
                    <div class="mb-1 d-flex justify-content-center">
                      <sup class="h5 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                      <h1 class="price-toggle price-yearly fw-bold h1 mb-0 text-primary">42</h1>
                      <h1 class="price-toggle price-monthly fw-bold h1 mb-0 d-none text-primary">49</h1>
                      <sub class="h5 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                    </div>
                    <small class="position-absolute start-0 end-0 m-auto price-yearly price-yearly-toggle text-muted">$ 499 / year</small>
                  </div>

                  <ul class="ps-3 my-4 list-unstyled">
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Up to 5 users</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> 120+ components</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Basic support on Github</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Monthly updates</li>
                    <li class="mb-0"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Integrations</li>
                  </ul>

                  <a href="{{url('auth/register-basic')}}" class="btn btn-primary d-grid w-100">Upgrade</a>
                </div>
              </div>
            </div>

            <!-- Enterprise -->
            <div class="col-lg">
              <div class="card border rounded shadow-none">
                <div class="card-body">

                  <div class="my-3 pt-2 text-center">
                    <img src="{{asset('assets/img/icons/unicons/briefcase-round.png')}}" alt="Pro Image" height="80">
                  </div>
                  <h3 class="card-title text-center text-capitalize fw-semibold mb-1">Enterprise</h3>
                  <p class="text-center">Solution for big organizations</p>

                  <div class="text-center mb-5">
                    <div class="mb-1 d-flex justify-content-center">
                      <sup class="h5 pricing-currency mt-3 mb-0 me-1 text-primary">$</sup>
                      <h1 class="price-toggle price-yearly fw-bold h1 mb-0 text-primary">84</h1>
                      <h1 class="price-toggle price-monthly fw-bold h1 mb-0 d-none text-primary">99</h1>
                      <sub class="h5 pricing-duration mt-auto mb-2 text-muted fw-normal">/month</sub>
                    </div>
                    <small class="position-absolute start-0 end-0 m-auto price-yearly price-yearly-toggle text-muted">$ 999 / year</small>
                  </div>

                  <ul class="ps-3 my-4 list-unstyled">
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Up to 10 users</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> 150+ components</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Basic support on Github</li>
                    <li class="mb-2"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Monthly updates</li>
                    <li class="mb-0"><span class="badge badge-center w-px-20 h-px-20 rounded-pill bg-label-primary me-2"><i class="bx bx-check bx-xs"></i></span> Speedy build tooling</li>
                  </ul>

                  <a href="{{url('auth/register-basic')}}" class="btn btn-label-primary d-grid w-100">Upgrade</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--/ Pricing Plans -->
      </div>
    </div>
  </div>
</div>
<!--/ Pricing Modal -->
