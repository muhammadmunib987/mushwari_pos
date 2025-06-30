<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action('\Modules\Partners\Http\Controllers\PartnerAssetsController@index')}}"><i class="fas fa-user"></i> Fixed Assets</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @if(auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all'))
                        <li @if(request()->segment(2) == 'partners') class="active" @endif>
                            <a href="{{action('\Modules\Partners\Http\Controllers\PartnersController@index')}}">
                               Partners
                            </a>
                        </li>
                    @endif

                    @can('job_sheet.create')
                        <li @if(request()->segment(2) == 'partners_pay') class="active" @endif>
                            <a href="{{action('\Modules\Partners\Http\Controllers\PaymentsController@index')}}">
                                Partner Payment Record
                            </a>
                        </li>
                            <li @if(request()->segment(2) == 'partners_set') class="active" @endif>
                                <a href="{{action('\Modules\Partners\Http\Controllers\FinalAccountController@index')}}">
                                    Final Account
                                </a>
                            </li>

                            <li @if(request()->segment(2) == 'partners_set') class="active" @endif>
                                <a href="{{action('\Modules\Partners\Http\Controllers\BusinessController@index')}}">
                                    Financial Estimate
                                </a>
                            </li>
                    @endcan

                   </ul>

            </div>
            <!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>