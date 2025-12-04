@extends('theme.page')

@php

@endphp
@section('title', 'Add new worker')
@section('content')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            /*background-color: #FFF;*/
            color: #333;
        }
        .select2-container--bootstrap5 .select2-selection--single .select2-selection__rendered{
            color: #181c32;
        }
    </style>
    <div class="d-flex flex-column flex-column-fluid" id="kt_content">

        <!--begin::Post-->
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="post d-flex flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class="container-xxl"> <!--container-xxl-->

                            <div class="mb-10">
                                <div class="bg-danger p-10 fw-boldest fs-1 text-white rounded" id="screen_title">
                                    Add new worker &gt; <span id="add_worker_screen_name">Basic info</span> (Step <span id="add_worker_no_of_screen">1</span> of 4)
                                </div>

                                <div class="bg-success p-10 fw-boldest fs-1 text-white rounded d-none" id="worker_created_message">
                                    Worker created!
                                </div>
                            </div>

                            {{--@include('workers.partials.add_worker.worker_tabs')--}}
                            <div class="card">
                                <div class="card-body">
                                    <form id="add_worker_form" enctype="multipart/form-data">
                                        @csrf
                                        <div class="tab-content">
                                            @include('workers.partials.add_worker.basic_info')

                                            @include('workers.partials.add_worker.addresses')

                                            @include('workers.partials.add_worker.rtws')

                                            @include('workers.partials.add_worker.dis_document')

                                            @include('workers.partials.add_worker.worker_created_tab')
                                        </div>
                                    </form>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    @yield('add_worker_basic_details_js')
    @yield('add_worker_addresses_js')
    @yield('add_worker_rtws_js')
    @yield('add_worker_document_js')

    <script>
        activeMenu('/worker-management');

        national_insurance_number.addEventListener('keyup',function (e) {
            if (e.keyCode !== 8) {
                if (this.value.length === 2 || this.value.length === 5 || this.value.length === 8 || this.value.length === 11) {
                    this.value = this.value += ' ';
                }
            }
        });

        bank_ifsc_code.addEventListener('keyup',function (e) {
            console.log(e.keyCode);
            if (e.keyCode !== 6) {
                if (this.value.length === 2 || this.value.length === 5) {
                    this.value = this.value += '-';
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

        $(document).on('click', '#add_worker_next_screen_btn', function () {
            let screen_name = $(this).attr('data-add_worker_screen_name');
            let no_of_screen = $(this).attr('data-add_worker_no_of_screen');
            let section = $(this).attr('data-section');

            $('.error').html('');
            $.ajax({
                type        : 'post',
                url         : 'check-worker-validation/'+section,
                data        : new FormData($("#add_worker_form")[0]),
                contentType : false,
                processData : false,
                cache       : false,
                success: function(response) {
                    $('html, body').animate({ scrollTop: 0 }, 'fast');

                    if(response.code === 200) {

                        $("#add_worker_screen_name").empty().append(screen_name);
                        $("#add_worker_no_of_screen").empty().append(no_of_screen);

                        if(screen_name === "Addresses") {
                            $("#basic_details_tab").addClass('d-none');
                            $("#addresses_tab").removeClass('d-none');
                        } else if(screen_name === "Right to Work") {
                            $("#addresses_tab").addClass('d-none');
                            $("#rtw_tab").removeClass('d-none');
                        } else if(screen_name === "Documents") {
                            $("#rtw_tab").addClass('d-none');
                            $("#document_tab").removeClass('d-none');
                        }
                    } else if (response.code === 500) {
                        toastr.error(response.message);
                    } else {
                        for(let i = 0; i < Object.keys(response.data).length; i++) {
                            $("#"+Object.keys(response.data)[i]+"_error").empty().append(response.data[Object.keys(response.data)[i]][0]);
                        }
                    }
                },
                error: function (response) {
                    toastr.error(response.statusText);
                }
            })
        })

        $("#add_worker_form").on('submit', function (e) {
            $(".error").html('');
            e.preventDefault();

            $("#add_worker_submit_btn").addClass('d-none');
            $("#add_worker_form_process").removeClass('d-none');

            $.ajax({
                type        : 'post',
                url         : '{{ url('create-worker-action') }}',
                data        : new FormData($("#add_worker_form")[0]),
                contentType : false,
                processData : false,
                success     : function (response) {
                    $('html, body').animate({ scrollTop: 0 }, 'fast');

                    $("#add_worker_submit_btn").removeClass('d-none');
                    $("#add_worker_form_process").addClass('d-none');

                    if(response.code === 200) {
                        $("#document_tab").addClass('d-none');
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
                    $("#add_worker_form_process").addClass('d-none');

                    toastr.error(response.statusText);
                }
            })
        });
    </script>
@endsection
