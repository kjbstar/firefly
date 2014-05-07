<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PostCreateProjectCommand extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firefly:create-project';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description
        = 'This command automatically fires when Firefly is created as a project and tells you what to do.';

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
        //
        $this->info("\n\n");
        $this->comment('To finish the installation of Firefly, follow these instructions:');
        $this->comment('https://github.com/JC5/firefly/wiki/Installation');
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
