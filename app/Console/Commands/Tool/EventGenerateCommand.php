<?php

namespace App\Console\Commands\Tool;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class EventGenerateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'event:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the missing events and listeners based on registration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $provider = new \App\Providers\EventServiceProvider(app());

        foreach ($provider->listens() as $event => $listeners) {
            if (! Str::contains($event, '\\')) {
                continue;
            }

            $this->callSilent('make:event', ['name' => $event]);

            foreach ($listeners as $listener) {
                $listener = preg_replace('/@.+$/', '', $listener);

                $this->callSilent('make:listener', ['name' => $listener, '--event' => $event]);
            }
        }

        $this->info('Events and listeners generated successfully!');
    }
}
