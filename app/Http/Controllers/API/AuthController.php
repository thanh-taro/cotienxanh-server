<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers
 * @author  Nguyen Tri Thanh <adamnguyen.itdn@gmail.com>
 */
class AuthController extends Controller
{

    /**
     * Register a new user
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validateRegister($request);

        $attributes = $this->registerAttributes($request);
        $user = $this->createUser($attributes);

        $token = auth()->login($user);

        return $this->responseWithUserAndToken($user, $token);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $this->credentials($request);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->responseWithUserAndToken(auth()->user(), $token);
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
     * Get the token array structure.
     *
     * @param  \App\User $user
     * @param  string    $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseWithUserAndToken($user, $token)
    {
        return response()->json([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('phone_number', 'password');
    }

    /**
     * Get the needed register attributes from the request.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    protected function registerAttributes(Request $request)
    {
        return $request->only('phone_number', 'password', 'name', 'age');
    }

    /**
     * Create a new user
     *
     * @param array $attributes
     *
     * @return \App\User
     */
    protected function createUser(array $attributes)
    {
        return User::create([
            'phone_number' => $attributes['phone_number'],
            'password' => Hash::make($attributes['password']),
            'name' => $attributes['name'],
            'age' => $attributes['age'],
        ]);
    }

    /**
     * Validate the user register request.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateRegister(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required|string|numeric|unique:users',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1',
        ]);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);
    }
}
