<div id="filter_detail_Section" class="alert alert-custom alert-secondary" role="alert" style="background-color: #a1a5b7;">
    <div class="alert-text fw-boldest text-white text-center">
        <a href="javascript:;" id="payroll_week_backward_btn" class="float-start">
{{--            <span class="svg-icon svg-icon-1 svg-icon-dark">--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">--}}
{{--                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">--}}
{{--                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>--}}
{{--                        <path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) "/>--}}
{{--                        <path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) "/>--}}
{{--                    </g>--}}
{{--                </svg>--}}
{{--            </span>--}}
            <i class="las la-angle-double-left" style="color: #181c32;font-size: 24px;"></i>
        </a>
        <span class="fs-3">
            <span id="cost_center_span" class="d-none">
                <span class="badge badge-warning ms-1">CC</span>
                <span id="selected_cost_center" class="ms-1">Cost center</span>
            </span>
            <span id="client_span" class="d-none">
                <span class="badge badge-warning ms-1">C</span>
                <span id="selected_client" class="ms-1">Client</span>
            </span>
            <span id="site_span" class="d-none">
                <span class="badge badge-warning ms-1">S</span>
                <span id="selected_site" class="ms-1">Site</span>
            </span>
            <span id="job_span" class="d-none">
                <span class="badge badge-warning ms-1">J</span>
                <span id="selected_job" class="ms-1">Job</span>
            </span>
            <span id="payroll_week_span" class="d-none">
                <span class="badge badge-warning ms-1">T</span>
                <span id="selected_payroll_week" class="ms-1">Payroll week</span>
            </span>
            <span id="worker_span" class="d-none">
                <span class="badge badge-warning ms-1">W</span>
                <span id="selected_worker" class="ms-1">worker</span>
            </span>
            <span id="no_filter_span">
                <span class="badge badge-warning ms-1">N</span>
                <span id="selected_no" class="ms-1">No filter selected</span>
            </span>
        </span>
        <a href="javascript:;" id="payroll_week_forward_btn" class="float-end">
{{--            <span class="svg-icon svg-icon-1 svg-icon-dark">--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">--}}
{{--                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">--}}
{{--                        <polygon id="Shape" points="0 0 24 0 24 24 0 24"/>--}}
{{--                        <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" id="Path-94" fill="#000000" fill-rule="nonzero"/>--}}
{{--                        <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" id="Path-94" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) "/>--}}
{{--                    </g>--}}
{{--                </svg>--}}
{{--            </span>--}}
            <i class="las la-angle-double-right" style="color: #181c32;font-size: 24px;"></i>
        </a>
    </div>
</div>

@section('filter_details_js')
    <script>
        function getSelectedDropdownOptionText() {
            const values = {};

            $('#payroll_week_number, #cost_center, #client, #site, #job, #worker').each(function() {
                const id = $(this).attr('id');
                const selectedText = $(this).find('option:selected').text() || '';
                values[id] = selectedText;
            });
            return values;
        }

        function getSelectedDropdownValues() {
            const values = {};

            $('#payroll_week_number, #cost_center, #client, #site, #job, #worker').each(function() {
                const id = $(this).attr('id');
                const value = $(this).val() || null;
                values[id] = value;
            });
            return values;
        }

        function manage_filter_details_section(filterValues) {

            if (filterValues.cost_center && filterValues.cost_center !== '' && filterValues.cost_center !== 'All...') {
                $('#cost_center_span').removeClass('d-none');
                $('#selected_cost_center').text(filterValues.cost_center);
            } else {
                $('#cost_center_span').addClass('d-none');
            }

            if (filterValues.client && filterValues.client !== '' && filterValues.client !== 'All...') {
                $('#client_span').removeClass('d-none');
                $('#selected_client').text(filterValues.client);
            } else {
                $('#client_span').addClass('d-none');
            }

            if (filterValues.site && filterValues.site !== '' && filterValues.site !== 'Select a client first...') {
                $('#site_span').removeClass('d-none');
                $('#selected_site').text(filterValues.site);
            } else {
                $('#site_span').addClass('d-none');
            }

            if (filterValues.job && filterValues.job !== '' && filterValues.job !== 'Select a site first...') {
                $('#job_span').removeClass('d-none');
                $('#selected_job').text(filterValues.job);
            } else {
                $('#job_span').addClass('d-none');
            }

            if (filterValues.payroll_week_number && filterValues.payroll_week_number !== '') {
                $('#payroll_week_span').removeClass('d-none');
                $('#selected_payroll_week').text(filterValues.payroll_week_number);
            } else {
                $('#payroll_week_span').addClass('d-none');
            }

            if (filterValues.worker && filterValues.worker !== '' && filterValues.worker !== 'All...') {
                $('#worker_span').removeClass('d-none');
                $('#selected_worker').text(filterValues.worker);
            } else {
                $('#worker_span').addClass('d-none');
            }

            const anyFilterSelected = Object.values(filterValues).some(value => value !== '');
            if (!anyFilterSelected) {
                $('#no_filter_span').removeClass('d-none');
            } else {
                $('#no_filter_span').addClass('d-none');
            }
        }

        $('#payroll_week_forward_btn').on('click', function () {
            let payroll_week_number_frd_drp = $('#payroll_week_number')
            const currentIndex = payroll_week_number_frd_drp.prop('selectedIndex');
            const options = $('#payroll_week_number option:not(:first)');
            if (currentIndex < options.length) {
                const nextIndex = currentIndex + 1;
                payroll_week_number_frd_drp.prop('selectedIndex', nextIndex);
            } else {
                payroll_week_number_frd_drp.prop('selectedIndex', 1);
            }
            payroll_week_number_frd_drp.trigger('change');
            $('#view_summary_btn').click();
        });

        $('#payroll_week_backward_btn').on('click', function () {
            let payroll_week_number_bcd_drp = $('#payroll_week_number')
            const currentIndex = payroll_week_number_bcd_drp.prop('selectedIndex');
            const options = $('#payroll_week_number option:not(:first)');
            if (currentIndex > 1) {
                const prevIndex = currentIndex - 1;
                payroll_week_number_bcd_drp.prop('selectedIndex', prevIndex);
            } else {
                payroll_week_number_bcd_drp.prop('selectedIndex', options.length);
            }

            payroll_week_number_bcd_drp.trigger('change');
            $('#view_summary_btn').click();
        });
    </script>
@endsection
