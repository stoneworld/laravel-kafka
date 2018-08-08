<?php
/**
 * @author andzone.
 */

namespace andZone\Kafka;


use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ConsumerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:consumer';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new consumer class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Consumer';
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return  __DIR__.'/stubs/consumer.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Console\Consumers';
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
