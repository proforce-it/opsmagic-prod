<div class="card mb-5">
    <div class="card-body py-4">
        <div class="row">
            <div class="col-8 d-flex align-items-center">
                <div>
                    <div class="d-flex align-items-center flex-wrap">
                        <a href="javascript:;" class="me-1">
                            <i class="las la-chevron-circle-up text-primary fs-xxl-1"></i>
                        </a>
                        <span class="fw-bolder fs-1">{{ $job['name'] }}</span>
                        <span class="fs-2 text-muted ms-3">{{ $job['client_details']['company_name'] }}</span>
                        <span class="text-muted ms-2"><i class="fs-3 las la-angle-right"></i></span>
                        <span class="fs-2 text-muted ms-2">{{ $job['site_details']['site_name'] }}</span>
                        @if($job['archived'] == '1')
                            <span class="text-danger fs-1">(Archived)</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        <a href="{{ url('view-client-job/'.$job['id'].'?view_type=details') }}" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 ps-2 pe-3 {{ ($view_type == 'details') ? 'active text-gray-100' : '' }}">
                            <i class="fs-xxl-1 las la-clipboard-list {{ ($view_type == 'details') ? 'text-gray-100' : 'text-primary' }}"></i>
                            <span class="fw-boldest">DETAILS</span>
                        </a>
                        <a href="{{ url('view-job-shift/'.$current_shift_id.'?view_type=day') }}" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 ps-2 pe-3 ms-1 {{ ($view_type == 'day') ? 'active text-gray-100' : '' }}">
                            <i class="fs-xxl-1 las la-calendar-day {{ ($view_type == 'day') ? 'text-gray-100' : 'text-primary' }}"></i>
                            <span class="fw-boldest">DAY</span>
                        </a>
                        <a href="{{ url('worker-availability/'.$job['id'].'?view_type=week') }}" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 ps-2 pe-3 ms-1 {{ ($view_type == 'week') ? 'active text-gray-100' : '' }}">
                            <i class="fs-xxl-1 las la-calendar-week {{ ($view_type == 'week') ? 'text-gray-100' : 'text-primary' }}"></i>
                            <span class="fw-boldest">WEEK</span>
                        </a>
                        <a href="{{ url('assignment-management?tag='.$job['client_id'].'.'.$job['site_id'].'.'.$job['id'].'&view_type=month') }}" class="btn btn-sm btn-outline btn-outline-primary text-hover-gray-100 ps-2 pe-3 ms-1 {{ ($view_type == 'month') ? 'active text-gray-100' : '' }}">
                            <i class="fs-xxl-1 las la-calendar {{ ($view_type == 'month') ? 'text-gray-100' : 'text-primary' }}"></i>
                            <span class="fw-boldest">MONTH</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-4 text-end">
                @if($job['client_details']['company_logo'])
                    <img src="{{ asset('workers/client_document/'.$job['client_details']['company_logo']) }}" alt="No image." style="width: 300px; height: 100px; object-fit: contain; object-position: right;">
                @endif
            </div>
        </div>
    </div>
</div>