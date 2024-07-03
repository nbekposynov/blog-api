<?php

namespace App\Http\Controllers;

use App\Contracts\AuthInterface;
use App\Contracts\PostInterface;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $token = $this->authService->register($validatedData);

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = $this->authService->login($credentials)) {
            return response()->json(['error' => 'Неправильные данные'], 401);
        }

        return response()->json(['token' => $token]);
    }
}