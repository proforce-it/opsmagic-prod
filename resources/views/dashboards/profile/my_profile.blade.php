@extends('theme.page')
@php
    $title = 'My Profile - '.\Illuminate\Support\Facades\Auth::user()->name;
@endphp
@section('title', $title)
@section('content')
    <style>
        .btn-light-primary-custom{
            background-color: #DAECF9;
        }

        .active {
            background-color: #FFF;
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
                    <div class="post flex-column-fluid" id="kt_post"> <!--d-flex-->
                        <div id="kt_content_container" class=""> <!--container-xxl-->
                            <div class="card mb-5">
                                <div class="card-body py-4">
                                    <div class="row">
                                        <div class="col-lg-1 col-md-2 col-sm-2" id="user_profile_pic">
                                            @if($user['profile_pic'])
                                                <img src="{{ asset('workers/users/'.$user['profile_pic']) }}" alt="" class="w-70px h-70px" style="object-fit: contain; object-position: center;">
                                            @else
                                                <i class="fs-xxl-2hx las la-user-alt bg-gray-200 rounded-3 p-2"></i>

                                                {{--<img src="{{ asset('assets/media/avatars/worker-square.png') }}" alt="" class="w-70px h-70px" style="object-fit: contain; object-position: center;">--}}
                                            @endif
                                        </div>
                                        <div class="col-lg-7 col-md-4 col-sm-4">
                                            <div class="fs-1 fw-bold">
                                                {{ \Illuminate\Support\Facades\Auth::user()->name}}
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6 col-sm-6 text-end pt-5">
                                            <a href="javascript:;" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                <i class="text-danger fs-2x las la-sign-out-alt"></i>
                                            </a>
                                            <form id="logout-form" action="{{ url('logout') }}" method="POST" style="display: none;">
                                                @if(config('adminlte.logout_method'))
                                                    {{ method_field(config('adminlte.logout_method')) }}
                                                @endif
                                                {{ csrf_field() }}
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <ul class="nav ms-10">
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm active" data-bs-toggle="tab" href="#kt_table_widget_5_tab_1" id="kt_table_widget_5_tab_1_menu">Preferences</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ms-1 btn btn-light-primary-custom btn-active btn-active-light-custom fw-bolder px-4 edthm" data-bs-toggle="tab" href="#kt_table_widget_5_tab_2" id="kt_table_widget_5_tab_2_menu">Password</a>
                                </li>
                            </ul>
                            <div class="card mt-n1">
                                <div class="card-body">
                                    <div class="tab-content">
                                        @include('dashboards.profile.partials.preferences_tab')
                                        @include('dashboards.profile.partials.password_tab')
                                    </div>
                                </div>
                                <!--end::Card body-->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('dashboards.profile.partials.profile_pic_model')

@endsection
@section('js')
    @yield('update-dashboard-tab-js')
    @yield('update-user-profile-pic-js')
    @yield('update-user-password-tab-js')
@endsection

