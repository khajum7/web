<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\CognitoException;
use App\Http\Controllers\Controller;
use App\Http\Services\CognitoService;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @return Response
     */
    public function resetPassword(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, CognitoService $cognitoService): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $user = User::where('email', $request->only('email'))->where('status', '1')->first();

            if (!$user) return back()->with('error', 'User not found!');

            return $cognitoService->requestResetPassword($request->get('email'));

        }catch (\Exception $exception){
            throw ValidationException::withMessages([
                'email' => [trans($exception->getMessage())],
            ]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function storeResetPassword(Request $request, CognitoService $cognitoService): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'string','min:12','regex:/[!@#$%^&*]/', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $response = $cognitoService->resetPassword($request->only('email', 'password', 'token'));
        }catch (CognitoException |\Exception $exception){
            throw ValidationException::withMessages([
                'email' => $exception->getMessage(),
            ]);
        }
        return $response;
    }
}
