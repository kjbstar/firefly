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


    private function _checkFoldersWriteable() {
        $folders = [
            'app/storage/cache',
            'app/storage/components',
            'app/storage/logs',
            'app/storage/meta',
            'app/storage/sessions',
            'app/storage/views',
        ];
        $error = false;
        foreach($folders as $folder) {
            if(!is_writable($folder)) {
                $error = true;
                $this->error('Folder '.$folder.' is not writeable.');
            }
        }
        return $error;

    }

    private function _checkDBConnection() {
        try {
            DB::select('SELECT 1;');
        } catch(PDOException $e) {
            $this->error('No database connection: ' . $e->getMessage());
            return true;
        }
        return false;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $error = false;

        // folders writeable?
        $error = $this->_checkFoldersWriteable();

        // database connection?
        $error = $error || $this->_checkDBConnection();

        if($error) {
            $this->comment('Please fix the errors listed above first. Then run this command again.');
            return;
        }

        // run migration and db:seed
        $this->call('migrate');
        $this->call('db:seed');

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
        $this->info("\n\n");
        $this->info('You can login using username ' . $user->username . ' and password '.$password);
        $this->info("\n\n");
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
