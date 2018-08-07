<?php
/**
 * @author : andzone
 */

namespace AndZone\Kafka;

class MessageProducer
{
    private $conf;
    private $producer;
    private $config;

    /**
     * MessageProducer constructor.
     * @param Conf $conf
     * @param $config
     */
    public function __construct(Conf $conf, $config)
    {
        $this->conf = $conf->bootConfig();
        $this->config = $config;
        $this->producer = $this->producer();
    }

    /**
     * @param $topic :messageType
     * @param $source :source of message
     * @param $data
     * @param $partition
     * @return bool
     */
    public function sendMessage($topic, $source, $data, $partition = RD_KAFKA_PARTITION_UA)
    {
        $topicConf = new \RdKafka\TopicConf();
        $topicConf->set('request.required.acks', 0);
        $objTopic = $this->producer->newTopic($topic, $topicConf);
        $message = [
            'topic' => $topic,
            'source' => $source,
            'timestamp' => time(),
            'data' => $data,
        ];
        $objTopic->produce($partition, 0, json_encode($message));
        while (($this->producer->getOutQLen()) > 0) {
            $this->producer->poll(50);
        }
        // TODO write log
        return true;
    }

    /**
     * @return \RdKafka\Producer
     */
    public function producer()
    {
        $producer = new \RdKafka\Producer($this->conf);
        $producer->setLogLevel($this->config['log_level']);
        $producer->addBrokers($this->config['server']);
        return $producer;
    }
}
