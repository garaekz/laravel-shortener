<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiUrlStoreRequest;
use App\Models\Url;
use App\Services\UrlService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                'data' => [
                    'code' => $code,
                    'original_url' => $url->original_url,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            ], 201);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
