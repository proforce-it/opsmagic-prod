"use strict";

// Class definition
let expenseList = function () {

    let datatable;
    let table

    let initExpenseList = function () {

        datatable = $(table).DataTable({
            "serverSide"    : false,
            "processing"    : false,
            "ajax"          : {
                "url"   : 'list-of-client',
                "type"  : 'post',
                "data"  : function (d) {
                    d._token    = $('#_token').val();
                    d.status    = $('input[name="client_status"]:checked').val();
                }
            },
            "columns": [
                { "data": "company_logo", "width":"25%"},
                { "data": "company_name"},
                { "data": "status", "width":"15%"},
                { "data": "no_of_sites", "width":"10%"},
                { "data": "flags"},
                { "data": "actions", "sClass": "text-end", "width":"15%"},
            ],
            "order": [[ 1, "asc" ]],
        });

        $(document).on('click', '#delete_customer', function () {
            Swal.fire({
                text                : 'Do you want to archive this client!',
                icon                : "warning",
                showCancelButton    : true,
                buttonsStyling      : false,
                confirmButtonText   : 'Yes, archive',
                cancelButtonText    : "No, cancel",
                customClass         : {
                    confirmButton       : "btn fw-bold btn-danger",
                    cancelButton        : "btn fw-bold btn-active-light-danger"
                }
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : 'delete-client-action/'+$(this).attr('data-client-id'),
                        success : function (response) {
                            if(response.code === 200) {
                                toastr.success(response.message);
                                datatable.ajax.reload();
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

        $(document).on('change', '.client_status', function () {
            datatable.ajax.reload();
        })
    }

    let handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-client-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            datatable.column(1).search(e.target.value).draw();
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
