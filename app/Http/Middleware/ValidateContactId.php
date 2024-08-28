<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateContactId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $contactId = $request->route('contactId');

        if (!is_numeric($contactId) || !$contactId) {
            throw new HttpResponseException(
                response()->json([
                    "errors" => [
                        "message" => "Contact is not found"
                    ]
                ])->setStatusCode(404)
            );
        }

        return $next($request);
    }
}
