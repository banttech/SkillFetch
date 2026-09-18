<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // EMPLOYER ONLY UPDATE
        if ($request->is('employer') || $request->is('employer/*')) {
            return route('employer.login.view');  
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login.view'); 
        }


        if ($request->is('supervisor') || $request->is('supervisor/*')) {
            return route('supervisor.login.view'); 
        }

        // Default Laravel login route (unchanged for admin)
        return route('login');
    }

}
