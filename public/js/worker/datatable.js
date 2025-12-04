"use strict";

// Class definition
let expenseList = function () {

    let datatable;
    let table

    let initExpenseList = function () {

        datatable = $(table).DataTable({
            "serverSide"    : false,
            "processing"    : false,
            "pagination"    : false,
            paging: false,
            scrollCollapse: true,
            scrollY: '800px',
            "ajax"          : {
                "url"   : 'list-of-workers',
                "type"  : 'post',
                "data"  : function (d) {
                    d._token    = $('#_token').val();
                    d.status    = $('input[name="worker_status"]:checked').val();
                    d.filter    = $('#filter').val();
                    d.cost_center = $('#cost_center').val();
                    /*d.first_name    = $("#first_name").val();
                    d.surname       = $("#surname").val();
                    d.mobile_number = $("#mobile_number").val();
                    d.email         = $("#email").val();
                    d.worker_no     = $("#worker_no").val();
                    d.status        = $("#status").val();*/
                },
            },
            "columns": [
                { "data": "worker_id"},
                { "data": "worker_name"},
                { "data": "status", "sClass": "text-center"},
                { "data": "right_to_work", "sClass": "text-center"},
                { "data": "mobile_number", "sClass": "text-center"},
                { "data": "flags"},
                { "data": "actions",  "sClass": "text-end"},
            ],
            "order": [[ 1, "desc" ]],
        });

        $(document).on('change', '.worker_status', function () {
            datatable.ajax.reload();
        })

        $(document).on('click', '#delete_worker', function () {
            Swal.fire({
                text                : $(this).attr('data-text'),
                icon                : "warning",
                showCancelButton    : true,
                buttonsStyling      : false,
                confirmButtonText   : $(this).attr('data-btn-text'),
                cancelButtonText    : "No, cancel",
                customClass         : {
                    confirmButton       : "btn fw-bold btn-"+$(this).attr('data-btn-color'),
                    cancelButton        : "btn fw-bold btn-active-light-danger"
                }
            }).then((result) => {
                let status = $(this).attr('data-worker-status');
                if (result.value) {
                    $.ajax({
                        type        : 'post',
                        url         : 'update-worker-status',
                        data        : {
                            _token      : $('#_token').val(),
                            worker_id   : $(this).attr('data-worker-id'),
                            status      : status,
                        },
                        success     : function (response) {
                            if(response.code === 200) {
                                toastr.success('Worker successfully '+status);
                                datatable.ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error   : function (response) {
                            toastr.error(response.statusText);
                        }
                    });
                }
            });
        });

        $("#filter_worker_button").on('click', function () {
            datatable.ajax.reload();
        });
    }

    let handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-worker-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            //datatable.column(1).search(e.target.value).draw();
            datatable.search(e.target.value).draw();
        });
    }

    return {
        init: function () {
            table = document.querySelector('#datatable');

            if (!table)
                return;

            initExpenseList();
            handleSearchDatatable();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    expenseList.init();
});
