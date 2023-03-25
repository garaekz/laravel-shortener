<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiUrlStoreRequest;
use App\Services\UrlService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UrlController extends Controller
{
    private $service;

    public function __construct(UrlService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ApiUrlStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $code = $this->service->generateUrlCode();

            $url = $this->service->create($data['url'], $code);

            return response()->json([
                'status' => 'success',
                'data' => $url,
            ], 201);

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        try {
            $url = $this->service->findByCode($code);
            return response()->json([
                'status' => 'success',
                'data' => $url,
            ]);
        } catch (\Throwable $th) {
            if ($th instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Url not found.',
                ], 404);
            }
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
