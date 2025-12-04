@extends('theme.page')

@section('title', 'View job details')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="card">
                                <!--begin::Card header-->
                                <div class="card-header border-0 pt-6">
                                    {{--<h3 class="card-title align-items-start flex-column">
                                        <span class="card-label fw-bolder fs-3 mb-1">Latest Products</span>
                                        <span class="text-muted mt-1 fw-bold fs-7">More than 400 new products</span>
                                    </h3>--}}
                                    <div class="card-toolbar">
                                        <ul class="nav">
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder active px-4  me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1">Basic details</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link btn btn-sm btn-color-muted btn-active btn-active-secondary fw-bolder px-4 me-1" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2">Worker details</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <!--end::Card header-->
                                <!--begin::Card body-->
                                <div class="card-body py-4">
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="kt_table_widget_5_tab_1">
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Job title</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['job_title'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Client</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['client_details']['company_name'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Category</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['category'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Job costing</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['job_costing'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Cost (£)</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ number_format($job['cost'], 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Job timeline</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['job_timeline'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Job start</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ date('d-m-Y h:i:s A', strtotime($job['job_start'])) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Job end</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ date('d-m-Y h:i:s A', strtotime($job['job_end'])) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Workers</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['number_of_workers'] }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Worker cost (£)</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ number_format($job['worker_cost'], 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-7">
                                                        <label class="col-lg-4 fw-bold text-muted">Details</label>
                                                        <div class="col-lg-8">
                                                            <span class="fw-bolder fs-6 text-gray-800">{{ $job['details'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="tab-pane fade" id="kt_table_widget_5_tab_2">
                                            <!--begin::Table container-->
                                            <div class="table-responsive">
                                                <div class="p-5">
                                                    <div class="row mb-10">
                                                        <div class="col-lg-12">
                                                            <table class="table table-bordered align-middle table-row-dashed fs-7 gy-3">
                                                                <thead>
                                                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                                                    <th class="min-w-125px">Name</th>
                                                                    <th>Category</th>
                                                                    <th>On Going Job</th>
                                                                    <th>Status</th>
                                                                    <th>Revenue</th>
                                                                    <th>Action</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody class="text-gray-600 fw-bold">
                                                                    @if($job['job_worker_details'])
                                                                        @foreach($job['job_worker_details'] as $row)
                                                                            @php($object  = $row['worker_details'])
                                                                            <tr>
                                                                                <td>{{ $object['first_name'].' '.$object['middle_name'].' '.$object['last_name'] }}</td>
                                                                                <td>-</td>
                                                                                <td>-</td>
                                                                                <td>{{ $object['status'] }}</td>
                                                                                <td>-</td>
                                                                                <td>
                                                                                    <a href="{{ url('view-worker-details/'.$object['id']) }}" target="_blank" data-id="'.$row['id'].'" class="btn btn-success btn-sm">View</a>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <!--end::Table-->
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        activeMenu('/assignment-management');

    </script>
@endsection
