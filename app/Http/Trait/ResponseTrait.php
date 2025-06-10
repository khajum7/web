<?php

namespace App\Http\Trait;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait ResponseTrait
{
    /**
     * @param null $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    public function successResponseWithData($message = null, array|Collection|Model|JsonResource $data = [], int $status= Response::HTTP_OK): JsonResponse
    {
        return \response()->json([
            'type'      => 'success',
            'message'   => $message,
            'data'      => $data
        ], $status);
    }

    /**
     * @param $message
     * @param int $status
     * @return JsonResponse
     */
    public function errorResponse($message = null, int $status = Response::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return \response()->json([
            'type'      => 'error',
            'message'   => $message
        ], $status);
    }

    /**
     * @param $message
     * @param int $status
     * @return JsonResponse
     */
    public function successResponse($message = null, int $status= Response::HTTP_OK): JsonResponse
    {
        return \response()->json([
            'type'      => 'success',
            'message'   => $message
        ], $status);
    }
}
