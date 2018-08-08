<?php
/**
 * @author andzone.
 */

namespace AndZone\Kafka;

use Illuminate\Support\ServiceProvider;

class KafkaServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ConsumerMakeCommand::class,
            ]);
        }
    }
    public function register()
    {
        $this->commands([
            ConsumerMakeCommand::class,
        ]);
    }
}
