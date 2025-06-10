<?php

namespace App\Http\Controllers\User;

use App\Exceptions\CognitoException;
use App\Http\Controllers\Controller;
use App\Http\Services\CognitoService;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{

    public function index()
    {
        $users = User::all();

        return Inertia::render('Users/Users', ['users' => $users]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request, CognitoService $cognitoService)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $cognitoService->createUser($user);

            event(new Registered($user));
            DB::commit();
        }catch (CognitoException|\Exception $exception){
            DB::rollBack();
            return back()->with('error', $exception->getMessage());
        }

        return redirect(route('users.index'))->with('success', 'User created successfully.');
    }

}
