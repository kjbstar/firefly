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

    private function createUser()
    {
        // ask for details:
        $username = $this->ask('What is your preferred user name?');
        $this->comment('Your email address is only used for password reset things.');
        $email = $this->ask('What is your email address?');


        // create user:
        $user = new \User([
            'username' => $username,
            'origin'   => '',
            'password' => '',
            'email'    => $email,
        ]);

        // validate it:
        $validator = Validator::make($user->toArray(),User::$rules);
        if($validator->fails()) {
            $this->error($validator->messages()->first());
            return false;
        }


        try {
            $user->save();
        } catch (QueryException $e) {
            $this->comment($e->getMessage());
            $this->error('A database exception was caught. Please try again.');
            return false;
        }
        return $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $user = false;
        while ($user === false) {
            $user = $this->createUser();
        }
        // set a password:
        $password = Str::random(12);
        $user->password = Hash::make($password);
        $user->save();
        echo "\n";
        $this->info('You can login using your new username ' . $user->username . ' and password '.$password);
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
