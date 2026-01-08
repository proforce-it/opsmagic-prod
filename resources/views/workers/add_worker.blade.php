@extends('theme.page')

@php

@endphp
@section('title', 'Add new worker')
@section('content')
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post">
                        <div id="kt_content_container" class="container-xxl">
                            <div class="mb-10">
                                <div class="bg-primary p-10 fs-3 text-white rounded" id="screen_title">
                                    <div class="small text-uppercase mb-1">ADD NEW ASSOCIATE</div>
                                    <h4 class="mb-0 fw-boldest text-white fs-1">Basic info</h4>
                                </div>
                                <div class="bg-success p-10 fw-boldest fs-1 text-white rounded d-none" id="worker_created_message">
                                    Associate created!
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <form id="add_worker_form">
                                        @csrf
                                        <div class="tab-content">
                                            @include('workers.partials.add_worker.basic_info')
                                            @include('workers.partials.add_worker.worker_created_tab')
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        activeMenu('/worker-management');

        national_insurance_number.addEventListener('keyup',function (e) {
            if (e.keyCode !== 8) {
                if (this.value.length === 2 || this.value.length === 5 || this.value.length === 8 || this.value.length === 11) {
                    this.value = this.value += ' ';
                }
            }
        });

        let d_date    = '{{ date('m', strtotime('-16 Years')) }}' + '-' + '{{ date('d', strtotime('-16 Years')) }}' + '-' + '{{ date('y', strtotime('-16 Years')) }}';
        let d_newDate         = new Date(d_date);
        let d_currentMonth    = d_newDate.getMonth();
        let d_currentDate     = d_newDate.getDate();
        let d_currentYear     = d_newDate.getFullYear();

        $("#date_of_birth").flatpickr({
            dateFormat  : "d-m-Y",
            maxDate     : new Date(d_currentYear, d_currentMonth, d_currentDate),
        });

        $("#add_worker_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#add_worker_submit_btn").addClass('d-none');
            $("#add_worker_process_btn").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-worker-action') }}',
                data        : new FormData($("#add_worker_form")[0]),
                contentType : false,
                processData : false,
                success     : function (response) {
                    $('html, body').animate({ scrollTop: 0 }, 'fast');

                    $("#add_worker_submit_btn").removeClass('d-none');
                    $("#add_worker_process_btn").addClass('d-none');

                    if(response.code === 200) {
                        $("#basic_details_tab").addClass('d-none');
                        $("#worker_created_tab").removeClass('d-none');
                        $("#screen_title").addClass('d-none');
                        $("#worker_created_message").removeClass('d-none');

                        $("#created_worker_name").text(response.data.worker_name);
                        $("#created_worker_edit_a").attr('href', `view-worker-details/${response.data.worker_id}`);
                    } else if(response.code === 500) {
                        toastr.error(response.message);
                    } else {
                        for (let i = 0; i < Object.keys(response.data).length; i++) {
                            if (i === 0) {
                                $("#"+Object.keys(response.data)[0]).focus();
                            }
                            $("#" + Object.keys(response.data)[i] + "_error").empty().append(response.data[Object.keys(response.data)[i]][0]);

                        }
                    }
                },
                error   : function (response) {
                    $("#add_worker_submit_btn").removeClass('d-none');
                    $("#add_worker_process_btn").addClass('d-none');

                    toastr.error(response.statusText);
                }
            })
        });
    </script>
@endsection
