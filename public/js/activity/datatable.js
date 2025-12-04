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
                "url"   : '../list-of-activity-logs',
                "type"  : 'post',
                "data"  : function (d) {
                    d._token        = $('#_token').val();
                    d.log_for_id    = $("#basic_details_update_id").val();
                    d.menu_type     = $("#menu_type").val();
                }
            },
            "columns": [
                { "data": "no", "sClass": "text-center"},
                { "data": "user"},
                { "data": "sub_heading"},
                { "data": "type"},
                { "data": "sub_type"},
                { "data": "field"},
                { "data": "old_value"},
                { "data": "new_value"},
                { "data": "created_at", "sClass": "text-center"},
            ],
            "order": [[ 0, "desc" ]],
        });
    }

    let handleSearchDatatable = () => {
        const filterSearch = document.querySelector('[data-kt-activity-log-table-filter="search"]');
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
