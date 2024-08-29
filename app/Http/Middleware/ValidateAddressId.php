<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAddressId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $addressId = $request->route('addressId');

        if (!is_numeric($addressId) || !$addressId) {
            throw new HttpResponseException(
                response()->json([
                    'errors' => [
                        "message" => "Address is not found"
                    ]
                ])->setStatusCode(404)
            );
        }
        return $next($request);
    }
}
