<?php

namespace App\Services\V1;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthService
{
    /**
     * Create a new class instance.
     */
    public function signup(array $data)
{
    // إنشاء المستخدم
    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

    // رجّع الموديل نفسه، وليس JsonResponse
    return $user;
}


   public function login(array $data)
    {
        // البحث عن المستخدم بناءً على الإيميل
        $user = User::where('email', $data['email'])->first();

        // التحقق من صحة الإيميل أو كلمة المرور
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid email or password'],
            ]);
        }

        // إنشاء الـ token
        $token = $user->createToken('login')->plainTextToken;

        return [
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ];
    }}
