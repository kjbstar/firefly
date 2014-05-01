<?php

use Illuminate\Console\Command;
use Illuminate\Database\QueryException as QueryException;

class PostInstallCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firefly:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will create the first user so you can login and use the tool.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        // set a password:
        $password = Str::random(12);
        $username = 'admin';

        $user = new User([
            'username' => $username,
            'password' => Hash::make($password),
            'email' => 'empty@local',
            'origin' => '',
        ]);
        try {
            $user->save();
        } catch(QueryException $e) {
            $this->error('The user "'.$username.'" already exists. Its password has been reset. Sorry about that.');
            $user = User::whereUsername($username)->first();
            $user->password = Hash::make($password);
            $user->save();
        }
        echo "\n";
        $this->info('You can login using username ' . $user->username . ' and password '.$password);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

}
