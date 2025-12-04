<div class="tab-pane fade {{ ($tabVar == 'alerts_and_bookings_tab' || $tabVar == '') ? 'active show' : '' }}" id="dashboard_tab_1">
    <div id="kt_content_container" >
        <div class="d-flex flex-column flex-column-fluid" id="kt_content"> <!--content -->
            <div class="post flex-column-fluid" id="kt_post">
                <div id="kt_content_container">
                    <div class="row d-none dashboard_section">
                        <div class="col-lg-6">
                            @include('dashboards.partials.bookings_and_alerts.quick_search')
                            @include('dashboards.partials.bookings_and_alerts.bookings')
                        </div>
                        <div class="col-lg-6">
                            @include('dashboards.partials.bookings_and_alerts.alerts')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
