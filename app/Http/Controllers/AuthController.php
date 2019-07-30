<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Auth Controller
 *
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    const REALM_ADMIN = 'admin';
    const REALM_STORE = 'store';

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = auth()->attempt($credentials);

        $isAuthorized = false;
        if ($token) {

            $realm = $request->get('realm', self::REALM_STORE);
            $level = auth()->user()->level;
            switch ($realm) {

                case self::REALM_ADMIN: $isAuthorized = ($level >= Role::STAFF_LEVEL); break;
                case self::REALM_STORE: $isAuthorized = ($level >= Role::CUSTOMER_LEVEL); break;
                default: $isAuthorized = false;
            }
        }

        return ($isAuthorized)?
            $this->respondWithToken($token) :
            response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
