<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Phishing Simulation Platform API",
 *     version="1.0.0",
 *     description="Comprehensive API for managing phishing simulation campaigns, employee training, and security awareness programs.",
 *     @OA\Contact(
 *         email="support@phishingsim.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter JWT token in format (Bearer <token>)"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Company authentication and authorization"
 * )
 * 
 * @OA\Tag(
 *     name="Company",
 *     description="Company management and dashboard"
 * )
 * 
 * @OA\Tag(
 *     name="Campaign",
 *     description="Phishing campaign management"
 * )
 * 
 * @OA\Tag(
 *     name="Payment",
 *     description="Payment processing and subscription management"
 * )
 * 
 * @OA\Tag(
 *     name="Reports",
 *     description="Campaign analytics and reporting"
 * )
 * 
 * @OA\Tag(
 *     name="Email Tracking",
 *     description="Public email tracking and phishing simulation"
 * )
 * 
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="errors", type="object", example={"field": {"Validation error message"}})
 * )
 * 
 * @OA\Schema(
 *     schema="Success",
 *     type="object",
 *     @OA\Property(property="message", type="string", example="Success message"),
 *     @OA\Property(property="data", type="object")
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}