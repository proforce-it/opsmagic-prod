<div class="tab-pane fade {{ ($tabVar == 'kpis_tab' || $tabVar == '') ? 'active show' : '' }}" id="dashboard_tab_2">
    <div id="kt_content_container" >
        <div class="d-flex flex-column flex-column-fluid" id="kt_content">
            <div class="post flex-column-fluid" id="kt_post">
                <div id="kt_content_container">
                    <div class="row d-none dashboard_section">
                        <div class="col-lg-6">
                            @include('dashboards.partials.kpis.week_snapshot')
                        </div>
                        <div class="col-lg-6">
                            @include('dashboards.partials.kpis.top_client')
                            @include('dashboards.partials.kpis.shifts_and_hours_trends')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
