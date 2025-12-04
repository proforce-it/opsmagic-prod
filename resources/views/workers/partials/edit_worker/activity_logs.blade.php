<div class="tab-pane fade" id="kt_table_widget_5_tab_10">
    <div class="table-responsive">
        <div class="p-5">
            <div class="row">
                <div class="col-lg-12">
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
{{--                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">--}}
{{--                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />--}}
{{--                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />--}}
{{--                            </svg>--}}
                                                                        <i class="fs-2 las la-search"></i>

                        </span>
                        <input type="text" data-kt-activity-log-table-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search activity logs" />
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-12 text-start text-muted fw-bolder fs-5 text-uppercase gs-0 border-bottom border-4"></div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="menu_type" id="menu_type" value="Worker">
                    @include('activity_logs.dis_logs')
                </div>
            </div>
        </div>
    </div>
</div>
