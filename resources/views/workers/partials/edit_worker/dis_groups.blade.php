<div class="tab-pane fade" id="kt_table_widget_5_tab_13">
    <div class="table-responsive">
        <div class="p-5">
            <div class="col-lg-12">
                <div class="alert alert-custom alert-info" role="alert">
                    <div class="alert-text fs-4">
                        <i class="las la-exclamation-circle text-info fs-xl-2"></i>
                        {{$worker['first_name']}} is a member of the group(s) listed below. Click the trash icon to unlinkl them from a group
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col-lg-12 text-end border-bottom gs-0 border-4">
                    <div class="float-end">
                        <a href="javascript:;" id="add_group_with_worker_modal_btn" class="me-4"><i class="las la-plus-circle text-primary fs-xxl-2qx"></i></a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table align-middle table-row-dashed fs-7 gy-3 bg-active-dark table-responsive" id="group_with_worker_datatable" data-worker-id="{{ $worker['id'] }}">
                        <thead>
                        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                            <th class="text-left">Group name</th>
                            <th class="text-left">Number of members</th>
                            <th class="text-end">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-bold"></tbody>
                    </table>
                </div>
            </div>
        </div>
        @include('groups.add_group_with_worker_modal')
    </div>
</div>
@section('group_js')
    <script>
        let group_with_worker_table;

        $(document).ready(function() {
            group_with_worker_table = $('#group_with_worker_datatable').DataTable({
                "processing": false,
                "serverSide": false,
                "ajax": {
                    "type": "post",
                    "url": '{{ url('get-group-with-worker') }}',
                    "data": function (d) {
                        d._token    = $('#_token').val();
                        d.worker_id = $('#worker_id').val();
                    },
                },
                "columns": [
                    {"data": "group_name"},
                    {"data": "number_of_members", "width":"10%", "sClass": "text-end"},
                    {"data": "action", "width":"10%", "sClass": "text-end"}
                ],
                "order": [[ 0, "asc" ]],
            });
        });

        $(document).on('click', '.unlink_group', function () {
            sweetAlertConfirmDelete('You want to unlink worker to this group?').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'post',
                        url     : '{{ url('unlink-group-with-worker') }}'+'/'+$(this).attr('data-group-with-worker_id'),
                        data    : {
                            _token: '{{ csrf_token() }}'
                        },
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                group_with_worker_table.ajax.reload();
                                setTimeout(function () {
                                    location.reload();
                                }, 1500)
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });
    </script>
    @yield('add_group_with_worker_js')
@endsection
