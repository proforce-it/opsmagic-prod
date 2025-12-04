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
                "url"   : 'list-of-assignments',
                "type"  : 'post',
                "data"  : {_token: $("#_token").val()}
            },
            "columns": [
                { "data": "job_title"},
                { "data": "client"},
                { "data": "category"},
                { "data": "job_timeline"},
                { "data": "actions", "sClass": "text-end"},
            ],
            "order": [[ 0, "desc" ]],
        });

        $(document).on('click', '#delete_job', function () {
            sweetAlertConfirmDelete('You want to delete this job!').then((result) => {
                if (result.value) {
                    $.ajax({
                        type    : 'get',
                        url     : 'delete-assignment-action/'+$(this).attr('data-job_id'),
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

    }

    let handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-job-table-filter="search"]');
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
