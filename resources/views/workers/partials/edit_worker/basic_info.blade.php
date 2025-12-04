<div class="tab-pane fade active show" id="kt_table_widget_5_tab_1">
    <div class="table-responsive">
        <form id="basic_details_form">
            @csrf
            <div class="p-5">
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Name</div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Title</label>
                            <select name="title" id="title" class="form-select form-select-lg" data-control="select2" data-placeholder="Select title" data-allow-clear="true">
                                <option {{ ($worker['title'] == "") ? 'selected' : '' }}></option>
                                <option {{ ($worker['title'] == "Mr") ? 'selected' : '' }} value="Mr">Mr</option>
                                <option {{ ($worker['title'] == "Ms") ? 'selected' : '' }} value="Ms">Ms</option>
                                <option {{ ($worker['title'] == "Mrs") ? 'selected' : '' }} value="Mrs">Mrs</option>
                                <option {{ ($worker['title'] == "Miss") ? 'selected' : '' }} value="Miss">Miss</option>
                            </select>
                            <span class="error text-danger" id="title_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">First name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $worker['first_name'] }}" placeholder="Enter first name">
                            <span class="error text-danger" id="first_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Middle Name</label>
                            <input type="text" name="middle_name" id="middle_name" class="form-control" value="{{ $worker['middle_name'] }}" placeholder="Enter middle name">
                            <span class="error text-danger" id="first_name_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Last name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ $worker['last_name'] }}" placeholder="Enter last name">
                            <span class="error text-danger" id="last_name_error"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Reference number</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Worker number</label>
                            <input type="text" name="worker_no" id="worker_no" class="form-control bg-secondary" value="{{ $worker['worker_no'] }}" placeholder="Enter worker no" readonly>
                            <input type="hidden" name="update_id" id="basic_details_update_id" value="{{ $worker['id'] }}">
                            <span class="error text-danger" id="worker_no_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Employee Ref.</label> <!--Client reference-->
                            <input type="text" name="client_reference" id="client_reference" class="form-control" value="{{ $worker['client_reference'] }}" placeholder="Enter client reference">
                            <span class="error text-danger" id="client_reference_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">National Insurance Number</label>
                            <input type="text" name="national_insurance_number" id="national_insurance_number" maxlength="13" class="form-control" placeholder="National Insurance Number" value="{{ $worker['national_insurance_number'] }}" />
                            <span class="error text-danger" id="national_insurance_number_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Payroll Ref.</label>
                            <input type="text" name="payroll_reference" id="payroll_reference" class="form-control" value="{{ $worker['payroll_reference'] }}" placeholder="Enter payroll reference">
                            <span class="error text-danger" id="payroll_reference_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Cost center</label>
                            <select name="cost_center" id="cost_center" class="form-select form-select-lg" data-control="select2" data-allow-clear="true" data-placeholder="Select...">
                                <option></option>
                                @if($costCentre)
                                    @foreach($costCentre as $cc_row)
                                        <option value="{{ $cc_row['id'] }}" {{ ($worker['worker_cost_center'] && $worker['worker_cost_center'][0]['cost_center'] == $cc_row['id']) ? 'selected' : '' }}>{{ $cc_row['short_code'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error text-danger" id="cost_center_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Contact details</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Mobile number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-mobile fs-xxl-2"></i>
                                    </span>
                                </div>
                                <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{ $worker['mobile_number'] }}" placeholder="Enter mobile number">
                            </div>
                            <span class="error text-danger" id="mobile_number_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Email address</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="las la-paper-plane fs-xxl-2"></i>
                                    </span>
                                </div>
                                <input type="text" name="email_address" id="email_address" class="form-control" value="{{ $worker['email_address'] }}" placeholder="Enter email address">
                            </div>
                            <span class="error text-danger" id="email_address_error"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Demographic data</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Date of birth</label>
                            <div class="position-relative d-flex align-items-center">
                                    <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                        <i class="fs-2 las la-calendar"></i>
                                    </span>
                                <input class="form-control ps-12 flatpickr-input date_input" placeholder="Select date of birth" name="date_of_birth" id="date_of_birth" type="text" readonly="readonly" value="{{ date('d-m-Y', strtotime($worker['date_of_birth'])) }}">
                            </div>
                            <span class="error text-danger" id="date_of_birth_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Gender</label>
                            <select name="gender" id="gender" class="form-select form-select-lg" data-control="select2" data-placeholder="Select gender" data-allow-clear="true">
                                <option {{ ($worker['gender'] == "") ? 'selected' : '' }}></option>
                                <option {{ ($worker['gender'] == "Male") ? 'selected' : '' }} value="Male">Male</option>
                                <option {{ ($worker['gender'] == "Female") ? 'selected' : '' }} value="Female">Female</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Marital status</label>
                            <select name="marital_status" id="marital_status" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option></option>
                                <option {{ ($worker['marital_status'] == "Married") ? 'selected' : '' }} value="Married">Married</option>
                                <option {{ ($worker['marital_status'] == "Single") ? 'selected' : '' }} value="Single">Single</option>
                                <option {{ ($worker['marital_status'] == "Co-habiting") ? 'selected' : '' }} value="Co-habiting">Co-habiting</option>
                            </select>
                            <span class="error text-danger" id="marital_status_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6 required">Nationality</label>
                            <select name="nationality" id="nationality" class="form-select form-select-lg" data-control="select2" data-placeholder="Select..." data-allow-clear="true">
                                <option></option>
                                @if($nationality)
                                    @foreach($nationality as $nRow)
                                        <option {{ ($worker['nationality'] == $nRow['nationality']) ? 'selected' : '' }} value="{{ $nRow['nationality'] }}">{{ $nRow['nationality'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="error text-danger" id="nationality_error"></span>
                        </div>
                    </div>
<!--                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Partner name</label>
                            <input type="text" name="name_of_partner" id="name_of_partner" class="form-control" value="{{ $worker['name_of_partner'] }}" placeholder="Enter name of partner">
                            <span class="error text-danger" id="name_of_partner_error"></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Partner worker no.</label>
                            <input type="text" name="id_number_of_partner" id="id_number_of_partner" class="form-control" value="{{ $worker['id_number_of_partner'] }}" placeholder="Enter id number of partner">
                            <span class="error text-danger" id="id_number_of_partner_error"></span>
                        </div>
                    </div>-->
                </div>
  <!--              <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4">Bank Details</div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank Sort Code</label>
                            <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" class="form-control" placeholder="Bank Sort Code" value="{{ $worker['bank_ifsc_code'] }}" minlength="8" maxlength="8" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank Account Number</label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control" placeholder="Bank Account Number" value="{{ $worker['bank_account_number'] }}" minlength="8" maxlength="8" />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-10 fv-row fv-plugins-icon-container">
                            <label class="fs-6">Bank Account Name</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Account Name" value="{{ $worker['bank_name'] }}" />
                        </div>
                    </div>
                </div>-->
                <div class="row mb-5">
                    <div class="col-lg-12 text-start fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <button type="submit" name="basic_details_submit" id="basic_details_submit" class="btn btn-primary btn-lg">Update</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('edit_worker_basic_details_js')
    <script>
        $("#basic_details_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();
            $.ajax({
                type        : 'post',
                url         : '{{ url('update-worker-basic-details') }}',
                data        : new FormData($(this)[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success     : function (response) {
                    decodeResponse(response)
                },
                error   : function (response) {
                    toastr.error(response.statusText);
                }
            });
        });
    </script>
@endsection
