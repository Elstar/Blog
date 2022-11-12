<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Command\Command as CommandAlias;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create user for use Bus faker';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $name = $this->ask('Put name please:');
        $email = $this->ask('Put e-mail please:');
        $type = '';
        while (!in_array($type, ['user', 'admin'])) {
            $type = $this->ask('Put user type: user or admin');
        }

        $password = $this->secret('Put password please:');
        User::create([
            'name' => $name,
            'email' => $email,
            'type' => $type,
            'password' => Hash::make($password),
        ]);
        $this->info("User {$email} successfully created");
        return CommandAlias::SUCCESS;
    }
}
