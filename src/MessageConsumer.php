<?php
/**
 * @author : andzone
 */

namespace AndZone\Kafka;

abstract class MessageConsumer
{
    private $consumer = null;

    protected $subscribedTopics = [];
    abstract function consumeMessage($message);

    private function getSubscribedTopics() {
        return $this->subscribedTopics;
    }

    public function handle() {
        $conf = new \RdKafka\Conf();
        $conf->set("api.version.request", true);
        $conf->setRebalanceCb(function (\RdKafka\KafkaConsumer $kafka, $err, array $partitions = null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    echo "Assign: ";
                    var_dump($partitions);
                    $kafka->assign($partitions);
                    break;
                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    echo "Revoke: ";
                    var_dump($partitions);
                    $kafka->assign(NULL);
                    break;
                default:
                    throw new \Exception($err);
            }
        });
        $group_id = get_called_class();
        $conf->set('group.id', $group_id);
        $conf->set('metadata.broker.list', '127.0.0.1');

        $topicConf = new \RdKafka\TopicConf();
        // Set where to start consuming messages when there is no initial offset in offset store or the desired offset is out of range. 'smallest': start from the beginning
        $topicConf->set('auto.offset.reset', 'largest');
        $conf->setDefaultTopicConf($topicConf);
        $consumer = new \RdKafka\KafkaConsumer($conf);
        $topics = $this->getSubscribedTopics();
        if (empty($topics)) {
            return false;
        }
        $consumer->subscribe($topics);
        $this->consumer = $consumer;
        while(true) {
            $message = $this->consumer->consume(120000);
            switch($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    // 消费消息
                    var_dump($message);
                    $this->consumeMessage($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Time out\n";
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
        echo "ending to consume messages.\n";
    }
}
