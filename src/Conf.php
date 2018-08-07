<?php
/**
 * @author : andzone
 */

namespace AndZone\Kafka;


class Conf
{
    private $conf;

    public function __construct($config)
    {
        $conf = new \RdKafka\Conf();
        $conf->set("api.version.request", true);
        $conf->set('socket.timeout.ms', 50); // or socket.blocking.max.ms, depending on librdkafka version
        if (function_exists('pcntl_sigprocmask')) {
            pcntl_sigprocmask(SIG_BLOCK, array(SIGIO));
            $conf->set('internal.termination.signal', SIGIO);
        } else {
            $conf->set('queue.buffering.max.ms', 1);
        }
        //Set delivery report callback
        $conf->setDrMsgCb(function ($kafka, $message) {
            file_put_contents("/tmp/dr_cb.log", var_export($message, true) . PHP_EOL, FILE_APPEND);
        });
        // Set error callback
        $conf->setErrorCb(function ($kafka, $err, $reason) {
            file_put_contents("/tmp/err_cb.log", sprintf("Kafka error: %s (reason: %s)", rd_kafka_err2str($err), $reason) . PHP_EOL, FILE_APPEND);
        });
        $this->conf = $conf;
    }

    public function bootConfig()
    {
        return $this->conf;
    }
}
