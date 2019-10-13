<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Add a new user to our database
     *
     * @param \Illuminate\Http\Request $request The HTTP request
     * @return \Illuminate\Http\Response Newly added user
     */
    public function register(Request $request)
    {
        $user = User::create([
             'email'    => $request->email,
             'name' => $request->name,
             'password' => $request->password,
         ]);

        $token = auth()->login($user);
        $user->access_token = $this->buildToken($token);

        return $user;
    }

    /**
     * Log in a user to get his token
     *
     * @return \Illuminate\Http\Response
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('email', $credentials['email'])->first();
        $user->access_token = $this->buildToken($token);

        return $user;
    }

    /**
     * Log out a user
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Build up full token data
     *
     * @param string $token JWT token
     * @return array Token-related info
     */
    protected function buildToken($token)
    {
        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ];
    }
}