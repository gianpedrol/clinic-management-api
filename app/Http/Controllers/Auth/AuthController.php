<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $data  = $request->only('email', 'password');
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not Found '], 404);
        }

        if ($user->status == 0) {
            return response()->json(['message' => 'User Inactive '], 403);
        }
        $token = auth()->login($user);;
        $passVerication = Hash::check($data['password'],  $user->password);

        if (empty($user) ||  $passVerication == false) {
            return response()->json(['status' => 'error', 'message' => 'the login is wrong'], 401);
        }
        if ($token) {
            $array['token'] = $token;
        } else {
            $array['message'] = 'Incorrect username or password';
        }

        return response()->json(['message' => "User Logged in!", 'token' => $array['token'], 'user' => $user], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
}
