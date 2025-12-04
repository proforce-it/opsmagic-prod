<div class="tab-pane fade" id="kt_table_widget_5_tab_6">
    <!--begin::Table container-->
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="w-100">
                    <div class="col-lg-12">
                        <div class="fw-bolder fs-3">
                            <div class="d-flex align-items-center position-relative my-1 mb-5">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <i class="fs-2 las la-search"></i>
                                </span>
                                <input type="text" data-kt-activity-log-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search activity logs" />
                            </div>
                            <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="menu_type" id="menu_type" value="Client">
                            @include('activity_logs.dis_logs')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Table-->
</div>