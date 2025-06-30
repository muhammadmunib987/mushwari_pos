<?php

namespace Modules\Connector\Http\Controllers\Api;

use App\Brands;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Connector\Transformers\CommonResource;
use Illuminate\Http\Request;          // ← inject if you need custom per-page
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @group Brand management
 * @authenticated
 *
 * APIs for managing brands
 */
class BrandController extends ApiController
{
    
    protected function jsonResponse($data){
    $isDataAvailable = !(
        is_null($data) ||
        (is_array($data) && empty($data)) ||
        ($data instanceof \Illuminate\Support\Collection && $data->isEmpty()) ||
        ($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection && $data->collection->isEmpty()) ||
        ($data instanceof \Illuminate\Pagination\LengthAwarePaginator && $data->isEmpty()) ||
        (is_string($data) && trim($data) === '')
    );

    return response()->json([
        'success' => $isDataAvailable,
        'message' => $isDataAvailable ? 'Get data successful' : 'No data found',
        'data'    => $data,
    ]);
}


    /**
     * List brands
     *
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                },
                {
                    "id": 2,
                    "business_id": 1,
                    "name": "Espirit",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:58",
                    "updated_at": "2018-01-03 21:19:58"
                }
            ]
        }
     */
  public function index(Request $request)
{
    $perPage     = (int) $request->input('per_page', 15);
    $business_id = Auth::user()->business_id;

    $paginator = Brands::where('business_id', $business_id)
                       ->orderBy('id')
                       ->paginate($perPage);

    // Transform current-page rows with your resource
    $payload = CommonResource::collection(
        collect($paginator->items())   // items() → array, wrap back to collection
    );

    return $this->paginatedResponse($paginator, $payload);
}



    /**
     * Get the specified brand
     *
     * @urlParam brand required comma separated ids of the brands Example: 1
     * @response {
            "data": [
                {
                    "id": 1,
                    "business_id": 1,
                    "name": "Levis",
                    "description": null,
                    "created_by": 1,
                    "deleted_at": null,
                    "created_at": "2018-01-03 21:19:47",
                    "updated_at": "2018-01-03 21:19:47"
                }
            ]
        }
     */
     public function show($brand_ids){
        $user = Auth::user();
        $business_id = $user->business_id;
    
        $brand_ids = explode(',', $brand_ids);
    
        $brands = Brands::where('business_id', $business_id)
                        ->whereIn('id', $brand_ids)
                        ->get();
    
        return $this->jsonResponse(CommonResource::collection($brands));
    }

}
