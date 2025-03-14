<?php

namespace SquareBoat\Sneaker\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Config\Repository;
use SquareBoat\Sneaker\Exceptions\DummyException;
use Symfony\Component\Console\Application as ConsoleApplication;

class Sneak extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sneaker:sneak';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if sneaker is working.';

    /**
     * The config implementation.
     *
     * @var \Illuminate\Config\Repository
     */
    private Repository $config;

    /**
     * Create a sneak command instance.
     *
     * @param  \Illuminate\Config\Repository $config
     * @return void
     */
    public function __construct(Repository $config)
    {
        parent::__construct();

        $this->config = $config;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->overrideConfig();

        try {
            app('sneaker')->captureException(new DummyException, true);

            $this->info('Sneaker is working fine ✅');

            if (($queue = config('queue.default')) !== 'sync') {
                $this->warn('The exception mail has been queued on the "'.$queue.'" queue.');
                $this->warn('Make sure you have queue workers running to deliver the email.');
            }
        } catch (Exception $e) {
            (new ConsoleApplication)->renderThrowable($e, $this->output);
        }
    }

    /**
     * Overriding the default configurations.
     *
     * @return void
     */
    public function overrideConfig()
    {
        $this->config->set('sneaker.capture', [DummyException::class]);
    }
}
