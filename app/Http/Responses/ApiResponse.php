<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * 外部 API の共通 JSON 形（data / meta）。
 */
final class ApiResponse
{
    public static function success(mixed $data, array $meta = []): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'meta' => $meta === [] ? new \stdClass : $meta,
        ]);
    }
}
