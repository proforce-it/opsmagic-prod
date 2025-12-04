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
            scrollY: '400px',
            "ajax"          : {
                "url"   : 'list-of-leaver-workers',
                "type"  : 'post',
                "data"  : {_token: $("#_token").val()}
            },
            "columns": [
                { "data": "worker_name"},
                { "data": "mobile_number", "sClass": "text-center"},
                { "data": "email_address", "sClass": "text-center"},
                { "data": "right_to_work", "sClass": "text-center"},
                { "data": "actions",  "sClass": "text-end"},
            ],
            "order": [[ 0, "desc" ]],
        });

        /*$(document).on('click', '#delete_worker', function () {
            sweetAlertConfirmDelete('You want to delete this worker!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : 'delete-worker-action/'+$(this).attr('data-worker-id'),
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
        });*/

    }

    let handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-worker-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
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
