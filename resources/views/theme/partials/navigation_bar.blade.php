<div id="kt_aside" class="aside" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_aside_mobile_toggle">
    <div class="aside-toolbar flex-column-auto kt_aside_toolbar" id="kt_aside_toolbar">
        <div class="aside-user d-flex align-items-sm-center justify-content-center py-5">
            <div class="symbol symbol-50px">
                @if(\Illuminate\Support\Facades\Auth::user()->profile_pic)
                    <img src="{{ asset('workers/users/'.\Illuminate\Support\Facades\Auth::user()->profile_pic) }}" alt="" class="w-50px h-50px" style="object-fit: contain; object-position: center;border: 3px solid #fff;">
                @else
                    <img src="{{ asset('assets/media/avatars/worker-square.png') }}" alt="" class="w-50px h-50px" style="object-fit: contain; object-position: center;border: 3px solid #fff;">
                @endif
            </div>
            <div class="aside-user-info flex-row-fluid flex-wrap ms-5">
                <div class="d-flex">
                    <div class="flex-grow-1 me-2">
                        <a href="javscript:;" class="text-white text-hover-primary fs-6 fw-bold">{{ Auth::user()->name }}</a>
                        <span class="text-gray-600 fw-bold d-block fs-10 mb-1">{{--{{ Auth::user()->email }}--}}</span>
                    </div>
                    <div class="me-n2">
                        <a href="#" class="btn btn-icon btn-sm btn-active-color-dark mt-n2" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-start" data-kt-menu-overflow="true">
                            <i class="text-white fs-2 las la-cog"></i>
                        </a>
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">
                                    <div class="symbol symbol-50px me-5">
                                        @if(\Illuminate\Support\Facades\Auth::user()->profile_pic)
                                            <img src="{{ asset('workers/users/'.\Illuminate\Support\Facades\Auth::user()->profile_pic) }}" alt="" class="w-50px h-50px">
                                        @else
                                            <img src="{{ asset('assets/media/avatars/worker-square.png') }}" alt="" class="w-50px h-50px">
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column">
                                        <div class="fw-bolder d-flex align-items-center fs-5">{{ Auth::user()->name }}
                                            <span class="badge badge-light-success fw-bolder fs-8 px-2 py-1 ms-2">{{ \Illuminate\Support\Facades\Auth::user()->user_type }}</span></div>
                                        <a href="#" class="fw-bold text-hover-primary fs-7">{{ Auth::user()->email }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="separator my-2"></div>
                            <div class="menu-item px-5">
                                <a href="{{url('/my-profile')}}" class="menu-link px-5">My Profile</a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="javascript:;" class="menu-link px-5" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a>
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
            </div>
        </div>
    </div>
    <div class="aside-menu flex-column-fluid">
        <div class="hover-scroll-overlay-y px-2 my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="{default: '#kt_aside_toolbar, #kt_aside_footer', lg: '#kt_header, #kt_aside_toolbar, #kt_aside_footer'}" data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="5px" style="height: 99px;">
            <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-dark menu-state-bullet-primary menu-arrow-gray-500" id="#kt_aside_menu" data-kt-menu="true">
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('dashboard') }}">
                        <span class="menu-icon">
                            <i class="tetx-white fs-2 las la-tachometer-alt"></i>
                        </span>
                        <span class="menu-title text-white">Dashboard</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8">
                        <span class="menu-section text-white text-uppercase fs-8 ls-1">Workers & clients</span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('client-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="text-white fs-2 las la-user-tie"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Clients</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('worker-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                              <i class="text-white fs-2 las la-user-friends"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Associates</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('groups') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                              <i class="text-white fs-2 las la-users"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Associate groups</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('worker-search') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="text-white fs-2 las la-search"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Associate search</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('worker-uploader') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                              <i class="text-white fs-2 las la-cloud-upload-alt"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Associates uploader</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8">
                        <span class="menu-section text-white text-uppercase fs-8 ls-1">Jobs and Bookings</span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('job-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-tools"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Jobs</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('job-shift-uploader') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-cloud-upload-alt"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Bookings uploader</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('assignment-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-calendar-day"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Bookings by job (month view)</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('shift-overview') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-calendar-week"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Bookings overview (week view)</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8">
                        <span class="menu-section text-white text-uppercase fs-8 ls-1">Timesheets & payroll</span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('timesheet-uploader') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-clock"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Timesheet uploader</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('bonus-uploader') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-coins"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Bonus uploader</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('timesheet-and-bonus-editor') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-edit"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Timesheet & bonus editor</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('view-payroll-report') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                               <i class="fs-2 las la-receipt"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Payroll report and export</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('financial-report') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-clipboard-check"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Financial report</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8">
                        <span class="menu-section text-white text-uppercase fs-8 ls-1">pending requests</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('absence-request') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fs-2 las la-clipboard-list"></i>
                                </span>
                            </span>
                        <span class="menu-title text-white">Absence requests</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('address-request') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fs-2 las la-map-pin"></i>
                                </span>
                            </span>
                        <span class="menu-title text-white">Addresses requests</span>
                    </a>
                </div>

                <div class="menu-item">
                    <div class="menu-content pt-8">
                        <span class="menu-section text-white text-uppercase fs-8 ls-1">Management & Settings</span>
                    </div>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('accommodation-list') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fs-2 las la-home"></i>
                                </span>
                            </span>
                        <span class="menu-title text-white">Accommodation sites</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('pick-up-point-list') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fs-2 las la-car-alt"></i>
                                </span>
                            </span>
                        <span class="menu-title text-white">Pick-up points</span>
                    </a>
                </div>

                @if(\Illuminate\Support\Facades\Auth::user()['user_type'] == 'Admin')
                    <div class="menu-item">
                        <a class="menu-link" href="{{ url('user-management') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <i class="fs-2 las la-user-circle"></i>
                                </span>
                            </span>
                            <span class="menu-title text-white">Users</span>
                        </a>
                    </div>
                @endif
                <div class="menu-item">
                    <a class="menu-link" href="{{ url('teams-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-user-friends"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Teams</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="{{ url('cost-centres-management') }}">
                        <span class="menu-icon">
                            <span class="svg-icon svg-icon-2">
                                <i class="fs-2 las la-map-marker"></i>
                            </span>
                        </span>
                        <span class="menu-title text-white">Cost centres</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
