<?php

namespace App\Http\Middleware;

use App\My_response\Traits\Response\JsonResponse;
use Closure;

class ValidateToken
{
    use JsonResponse;
    public function handle($request, Closure $next)
    {
        $user = auth('api')->user();

        if (!$user) {
            return self::unauthorizedError();
        }

        $incomingToken = $request->bearerToken();

        if ($incomingToken !== $user->token) {
            return self::unauthorizedError('Your token is expired or invalid.');
        }

        return $next($request);
    }
}
