<?php

namespace App\Http\Middleware;
use \App\Models\User;
use Closure;

class Login
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = User::where('name',$request->username)
            ->first();
        if(is_object($user)){
            $input['username'] = $user->email;
            $request->merge($input);
        }

        return $next($request);
    }

}