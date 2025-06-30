<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    @include('layouts.partials.css')

    @include('layouts.partials.extracss_auth')
    <script src='https://www.google.com/recaptcha/api.js'></script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body st class="pace-done" data-new-gr-c-s-check-loaded="14.1172.0" data-gr-ext-installed="" cz-shortcut-listen="true">
    @inject('request', 'Illuminate\Http\Request')
    @if (session('status') && session('status.success'))
        <input type="hidden" id="status_span" data-status="{{ session('status.success') }}"
            data-msg="{{ session('status.msg') }}">
    @endif
    <div class="container-fluid">
        <div class="row eq-height-row">
            <div class="col-md-12 col-sm-12 col-xs-12 right-col tw-pb-10 tw-px-5" style="padding: 0px;">
                <div class="tw-relative tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-py-4 tw-shadow-lg">
                    <div class="tw-container tw-mx-auto tw-flex tw-justify-between tw-items-center" style="padding: 10px;">
                        <div class="tw-flex tw-items-center tw-gap-4">
                            <div
                                class="lg:tw-w-16 md:tw-h-16 tw-w-12 tw-h-12 tw-flex tw-items-center tw-justify-center tw-overflow-hidden tw-bg-white tw-rounded-full tw-p-0.5">
                                <img src="https://business.jadidkhata.com/img/logo-small.png" alt="lock" class="tw-rounded-full tw-object-fill" />
                            </div>
                            
                            <div style="width: 11rem;" class="tw-border-2 tw-border-white tw-rounded-full tw-h-10 md:tw-h-12 tw-w-24 tw-flex tw-items-center tw-justify-center">
                                <a title="Under the 10TH tab (POS Invoice Verification)" href="https://iris.fbr.gov.pk/#verifications"
                                class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white">
                                {{ __('Verify FBR Invoice') }}
                                </a>
                            </div>
                         
                            @include('layouts.partials.language_btn')

                            @if(Route::has('repair-status'))
                                <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                href="{{ action([\Modules\Repair\Http\Controllers\CustomerRepairStatusController::class, 'index']) }}">
                                    @lang('repair::lang.repair_status')
                                </a>
                            @endif
                        </div>

                        <div class="tw-flex tw-items-center tw-gap-4">
                            @if (!($request->segment(1) == 'business' && $request->segment(2) == 'register'))
                                <!-- Register Url -->
                                @if (config('constants.allow_registration'))
                                    {{-- <span class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base">
                                        {{ __('business.not_yet_registered') }}
                                    </span> --}}

                                    <div class="tw-border-2 tw-border-white tw-rounded-full tw-h-10 md:tw-h-12 tw-w-24 tw-flex tw-items-center tw-justify-center">
                                        <a href="{{ route('business.getRegister')}}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif"
                                        class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white">
                                        {{ __('business.register') }}
                                        </a>
                                    </div>

                                    <!-- Pricing Url -->
                                    @if (Route::has('pricing') && config('app.env') != 'demo' && $request->segment(1) != 'pricing')
                                        &nbsp; <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                                href="{{ action([\Modules\Superadmin\Http\Controllers\PricingController::class, 'index']) }}">
                                                @lang('superadmin::lang.pricing')
                                        </a>
                                    @endif
                                @endif
                            @endif

                            @if ($request->segment(1) != 'login')
                                <a class="tw-text-white tw-font-medium tw-text-sm md:tw-text-base hover:tw-text-white"
                                href="{{ action([\App\Http\Controllers\Auth\LoginController::class, 'login'])}}@if(!empty(request()->lang)){{'?lang='.request()->lang}}@endif">
                                {{ __('business.sign_in') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
        <br>
                @yield('content')
            </div>
        </div>

        <footer class="row footer">
            <h3> For any Assistance 24/7 SUPPORT Please Call/Whatsapp: +44 7743 137758</h3>
        </footer>
    </div>


    @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>

    @yield('javascript')

    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2_register').select2();

            // $('input').iCheck({
            //     checkboxClass: 'icheckbox_square-blue',
            //     radioClass: 'iradio_square-blue',
            //     increaseArea: '20%' // optional
            // });
        });
    </script>
    <style>
        .wizard>.content {
            background-color: white !important;
        }
       
         body {
            background-image: url('https://business.jadidkhata.com/img/bg_img.jpeg') !important;
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            height: auto;
        } 
        .footer {
            text-align: center;
            font-weight: bold;
            font-size: larger !important;
            background-color: orange;

            bottom: 0;
            height: 60px; /* Adjust the height as needed */
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1); /* Optional shadow for better visual */
             
        }
        .footer h3 {
            margin: 0;
        }

    </style>
</body>

</html>
