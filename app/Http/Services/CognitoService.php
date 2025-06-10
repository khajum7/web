<?php

namespace App\Http\Services;

use App\Exceptions\CognitoException;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 *  Cognito Service
 *  Author: ST
 */
class CognitoService
{
    private PendingRequest $http;
    private string $cognitoUrl;
    private string $storeId;
    private array $headers;
    private string $name;

    public function __construct()
    {
        $this->cognitoUrl   = config('app.cognito.url');
        $this->storeId      = config('app.cognito.storeId');
        $this->name         = config('app.name');
        $this->headers      = [
            'domain' => config('app.cognito.domain')
        ];
        $this->http = Http::withHeaders($this->headers);
    }

    /**
     * @param User $user
     * @return string|null
     * @throws CognitoException
     *
     * Create Cognito user through provided data
     */
    public function createUser(User $user): null|string
    {
        try {
            $response = $this->http->post($this->cognitoUrl.'/cognito-user/create', [
                "email"          => $user->email,
                "badgeId"        => $user->email,
                "memberId"       => $user->id,
                "name"           => $user->name,
                "email_verified" => "true"
            ]);

            $responseBody = json_decode($response->body());
            Log::info($response->body());

            $cognitoUserName = data_get($responseBody, 'data.cognito_username');
            return $cognitoUserName ?? throw new CognitoException('Could not create user.');
        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception));
        }
    }

    /**
     * @param array $loginData
     * @return string|bool
     * @throws CognitoException
     *
     * Login Through cognito
     */
    public function login(array $loginData = []): string | bool
    {
        try {
            $email      = data_get($loginData, 'email');
            $password   = data_get($loginData, 'password');
            $user       = User::where([['email', $email ], ['status', '1']])->first();

            if(!$user) throw new CognitoException('User not found.');

            $response = $this->http->post($this->cognitoUrl.'/cognito-user/login', [
                'email'     => $email,
                'password'  => $password,
            ]);

            $responseBody   = json_decode($response->body());
            $token          = data_get($responseBody, 'data.cognito_session');

            if($token) return $token;

            $this->checkStatus($responseBody);
            $user = User::where([['email', data_get($responseBody, 'data.email')], ['status', '1']])->first();

            if(!$user) throw new CognitoException('User not found.');

            Auth::login($user);

            return true;
        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception));
        }
    }

    /**
     * @throws ValidationException
     *
     * Check Response status
     */
    public function checkStatus($response): true
    {
        $status = data_get($response, 'status');
        if($status == 'success') return true;

        throw ValidationException::withMessages([
            data_get($response, 'data')
        ]);
    }

    /**
     * @throws CognitoException
     *
     * Change password forcefully on first time
     */
    public function forcedPasswordChanged(array $data = []): Application|Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        try {
            $response = $this->http->post($this->cognitoUrl.'/cognito-user/force-password-change', [
                'email' => data_get($data, 'email'),
                'cognito_session' => data_get($data, 'token'),
                'password'  => data_get($data, 'password'),
            ]);

            $responseBody   = json_decode($response->body());
            $status         = data_get($responseBody, 'status', 'error');

            if ($status == 'error') {
                throw ValidationException::withMessages([
                    data_get($response, 'message', 'Something went wrong. Please try again.')
                ]);
            }

        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception, $response));
        }

        return redirect(route('login'))->with('success', 'Your password has been changed.');
    }

    /**
     * @param $email
     * @return RedirectResponse
     *
     * Request for rest password
     */
    public function requestResetPassword($email): RedirectResponse
    {
        try {
            $response = $this->http->post($this->cognitoUrl.'/cognito-user/forgot-password', [
                'email' => $email,
            ]);

            $responseBody = json_decode($response->body());
            $status = data_get($responseBody, 'status');

            return back()->with($status, data_get($responseBody, 'message'));
        }catch (\Exception $exception){
            $this->getMessageAndLog($exception);
            return back()->with('error', "Something went wrong. Please try again.");
        }
    }

    /**
     * @throws CognitoException
     *
     * Reset password
     */
    public function resetPassword(array $data = []): RedirectResponse
    {
        try {
            $response = $this->http->post($this->cognitoUrl.'/cognito-user/reset-password', [
                'email' => $data['email'],
                'password' => $data['password'],
                'code' => $data['token'],
            ]);

            $responseBody = json_decode($response->body());
            $status = data_get($responseBody, 'status', 'error');

            if ($status != 'success') {
                throw ValidationException::withMessages([
                    data_get($response, 'message', 'Something went wrong. Please try again.')
                ]);
            }
        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception, $response));
        }

        return redirect(route('login'))->with('success', "Your password has been reset. Please login again.");
    }

    /**
     * @throws CognitoException
     *
     * Update password of user
     */
    public function changePassword(array $data = []): RedirectResponse
    {
        try {
            $token = $this->getToken();

            if (!$token) return back()->with('error', 'Something went wrong. Please try again.');

            $this->headers['Authorization'] = 'Bearer ' . $token;

            $response = Http::withHeaders($this->headers)->post($this->cognitoUrl.'/cognito-user/change-password', [
                'email'             => $data['email'],
                'currentPassword'   => $data['currentPassword'],
                'newPassword'       => $data['newPassword'],
            ]);

            $responseBody = json_decode($response->body());
            $status = data_get($responseBody, 'status');

            if ($status != 'success') {
                throw ValidationException::withMessages([
                    data_get($response, 'message', 'Something went wrong. Please try again.')
                ]);
            }

        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception, $response));
        }

        return back()->with('success', "Your password has been reset. Please login again.");
    }

    /**
     * @throws CognitoException
     *
     * Get token from register
     */
    private function getToken(): string | null
    {
        try {
            $response = $this->http->post($this->cognitoUrl.'/app/register', [
                'storeId'  => $this->storeId,
                'name'     => $this->name,
                'status'   => 'active',
                'storeUrl' => $this->cognitoUrl,
            ]);

            $responseBody = json_decode($response->body());
            return data_get($responseBody, 'data.token');

        }catch (\Exception $exception){
            throw new CognitoException($this->getMessageAndLog($exception));
        }
    }

    /**
     * @param \Exception $exception
     * @param string $response
     * @return string
     *
     * Logged error and return message
     */
      private function getMessageAndLog(\Exception $exception, $response = ''): string
      {
          $message = $exception->getMessage();

          if(isset($response) && $response != ''){
              $responseBody = json_decode($response->body());
              $message = json_decode(data_get($responseBody, 'data')) ?? $message;
          }

          Log::error($message);
          return $message;
      }
}
