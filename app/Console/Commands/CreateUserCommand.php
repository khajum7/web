<?php

namespace App\Console\Commands;

use App\Http\Services\CognitoService;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(CognitoService $cognitoService)
    {
        try {
            $name = $this->ask('What is the user\'s name?');
            $email = $this->ask('What is the user\'s email?');

            try {
                DB::beginTransaction();

                $user = User::create([
                    'name' => $name,
                    'email' => $email
                ]);

                $cognitoService->createUser($user);

                DB::commit();
                $this->info("User created");
                $this->info('Email has been send. Please check you email.');
            }catch (\Exception $exception){
                DB::rollBack();
                $this->error($exception->getMessage());
            }


        }catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
