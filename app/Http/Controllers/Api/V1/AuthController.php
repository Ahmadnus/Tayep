<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\V1\AuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\SignupRequest;
use App\Http\Requests\V1\LoginRequest;
use App\Http\Resources\V1\SignupResource;
use App\Http\Resources\V1\LoginResource;
use Exception;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService)
    {
    }

    /**
     * تسجيل حساب جديد
     */
    public function signup(SignupRequest $request)
    {
        try {
            $user = $this->authService->signup($request->validated());

          
            return (new SignupResource($user))
                ->additional([
                    'message' => 'أدخل الرمز الذي أرسلناه لك عبر تيليغرام لتأكيد الحساب ✉️'
                ])
                ->response()
                ->setStatusCode(201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب، حاول مرة أخرى لاحقاً ⚠️',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request->validated());

            return (new LoginResource((object)[
                'id' => $data['user']->id,
                'name' => $data['user']->name,
                'email' => $data['user']->email,
                'token' => $data['token']
            ]))
                ->additional([
                    'message' => 'تحقق من تيليغرام 📩 وأدخل الرمز لتسجيل الدخول'
                ])
                ->response()
                ->setStatusCode(200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل تسجيل الدخول 😕 تحقق من المعلومات وحاول مجدداً',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 400);
        }
    }
}
