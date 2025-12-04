<div class="card card-bordered card-shadow">
    <div class="card-header bg-gray-300 min-h-50px">
        <div class="card-title">
            <span class="fs-2">QUICK SEARCH</span>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="fv-row fv-plugins-icon-container">
                    <select name="quick_search_worker_client_job" id="quick_search_worker_client_job" class="form-select form-select-lg form-select-custom" data-control="select2" data-placeholder="Worker / Client / Job" data-allow-clear="true" multiple>

                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

@section('quick_search_js')
    <script>
        $(function() {
            $("#quick_search_worker_client_job").select2({
                ajax: {
                    url: '{{ url('quick-dashboard-search-worker-client-job') }}',
                    dataType: 'json',
                    type: "post",
                    cache: false,
                    data: function (params) {
                        return {
                            _token: '{{ csrf_token() }}',
                            cost_center: $("#cost_center").val(),
                            keyword: params
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.data
                        };
                    }
                },
                templateResult: formatSearchResult,
                escapeMarkup: function (markup) {
                    return markup;
                }
            });
        });

        $("#quick_search_worker_client_job").on('select2:select', function (e) {
            let selected = e.params.data;
            if (selected && selected.url) {
                window.location.href = selected.url;
            }
        });

        function formatSearchResult(data) {
            if (!data.id)
                return data.text;

            return `<div class="select2-link" data-url="${data.url}">
                <a href="javascript:;" class="text-dark">${data.text}</a>
            </div>`;
        }
    </script>
@endsection
