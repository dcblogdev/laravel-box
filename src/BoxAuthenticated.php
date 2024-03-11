<?php

namespace Dcblogdev\Box;

use Closure;
use Dcblogdev\Box\Facades\Box;
use Illuminate\Http\Request;

class BoxAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (! Box::isConnected()) {
            return Box::connect();
        }

        return $next($request);
    }
}
