<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    protected $statusCode;

    protected $perPage;

    public function __construct()
    {
        $this->perPage = 10;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function respondUnauthorized($message = 'Unauthorized action.')
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    public function respond($data)
    {
        return response()->json($data);
    }

    public function modelNotFoundExceptionResult($e)
    {
        return $this->setStatusCode(404)->respondWithError($e->getMessage());

        // return [
        //         'status' => 404,
        //         'class' => method_exists($e, 'getModel') ? $e->getModel() : '',
        //         'value' => method_exists($e, 'getIds') ? $e->getIds() : '',
        //         'message' =>
        //     ];
    }

    public function otherExceptions($e)
    {
        $msg = is_object($e) ? $e->getMessage() : $e;

        return $this->setStatusCode(400)->respondWithError($msg);

        // return [
        //         'status' => 400,
        //         'message' => $e->getMessage()
        //     ];
    }

    protected function respondWithError($message)
    {
        return response()->json([
            'error' => [
                'message' => $message,
            ],
        ], $this->getStatusCode());
    }

    /**
     * Retrieves current passport client from request
     */
    public function getClient()
    {
        $client = request()->user()->token()->client;

        if(!empty($client) && !empty($client->name)){
            return $client->name;
        } else{
            return '';
        }
    }
    
    protected function paginatedResponse(LengthAwarePaginator $paginator, $data): JsonResponse
    {
        $hasData = $paginator->total() > 0;

        return response()->json([
            'success' => $hasData,
            'message' => $hasData ? 'Get data successful' : 'No data found',
            'data'    => $data,

            // ── standard pagination keys ─────────────────────────────
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last'  => $paginator->url($paginator->lastPage()),
                'prev'  => $paginator->previousPageUrl(),
                'next'  => $paginator->nextPageUrl(),
            ],
        ]);
    }
}
