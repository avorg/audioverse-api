<?php

namespace App\Providers;
use Dingo\Api\Auth\Provider\Authorization;
use Dingo\Api\Routing\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DingoPassportServiceProvider extends Authorization
{
    /**
     * Get the providers authorization method.
     *
     * @return string
     */
    public function getAuthorizationMethod()
    {
        return 'bearer';
    }
    /**
     * Authenticate the request and return the authenticated user instance.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Dingo\Api\Routing\Route $route
     *
     * @return mixed
     */
    public function authenticate(Request $request, Route $route)
    {
        if ($request->bearerToken()) {
            $this->validateAuthorizationHeader($request);
        } 
        return $request->user();
    }
}