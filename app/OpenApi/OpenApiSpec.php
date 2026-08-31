<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Product Inventory API',
    description: 'REST API for Product Inventory Management System'
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Local API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Sanctum Token'
)]
class OpenApiSpec
{
}
