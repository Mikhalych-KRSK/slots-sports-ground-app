<?php

namespace App\OpenApi\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Slots Sports Ground API",
 *     version="1.0.0",
 *     description="REST API для бронирования временных слотов на спортивной площадке."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Основной сервер"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="api_token",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class OpenApiInfo {}
