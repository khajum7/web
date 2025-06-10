<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\CognitoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     * @throws ValidationException
     */
    public function update(Request $request, CognitoService $cognitoService): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string','min:12','regex:/[!@#$%^&*]/', Password::defaults(), 'confirmed'],
        ]);

        try {
            return $cognitoService->changePassword([
                'email' => $request->user()->email,
                'currentPassword' => $request->get('current_password'),
                'newPassword' => $request->get('password'),
            ]);
        }catch (\Exception $exception){
            throw ValidationException::withMessages([
                'email' => $exception->getMessage(),
            ]);
        }
    }
}
