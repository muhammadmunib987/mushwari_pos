<?php

namespace Modules\Partners\Http\Controllers;
use App\Category;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Routing\Controller;
use Menu;
use Modules\Essentials\Entities\EssentialsTodoComment;
use Modules\Essentials\Entities\DocumentShare;
use Illuminate\Support\Facades\DB;
use Modules\Essentials\Entities\ToDo;
use Modules\Essentials\Entities\EssentialsHoliday;
use Modules\Essentials\Entities\EssentialsLeave;
use Modules\Essentials\Entities\Reminder;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
	 
	 public function superadmin_package()
    {
        return [
            [
                'name' => 'partners_module',
                'label' => 'Partner Management',
                'default' => false
            ]
        ];
    }
	 
    public function index()
    {
        return view('partners::index');
    }


    public function modifyAdminMenu()
    {


        $background_color = '#fff !important';

//dd(request()->segment(1));

            if (auth()->user()->can('partners.view')) {
           Menu::modify('admin-sidebar-menu', function ($menu) use ($background_color) {
                $menu->url(
                    action('\Modules\Partners\Http\Controllers\PartnersController@index'),
                   __('partners::lang.partner_pnn'),
                    ['icon' => 'fa fas fa-user', 'active' => request()->segment(1) == 'partners']
                )
                    ->order(88);
            });
			}


        }

    public function user_permissions()
    {
        return [
            [
			'value' => 'partners.view',
                'label' => __('partners::lang.show'),
                'default' => false
            ],
            [
                'value' => 'partners.create',
                'label' =>  __('partners::lang.creat'),
                'default' => false
            ],
            [
                'value' => 'partners.delete',
                'label' =>  __('partners::lang.delete'),
                'default' => false
            ],

            [
                'value' => 'partners.update',
                'label' => __('partners::lang.update'),
                'default' => false
            ],
        ];
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('partners::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        return view('partners::view');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        return view('partners::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
