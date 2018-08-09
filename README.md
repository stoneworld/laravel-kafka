### laravel-kafka
#### kafka文章归档
列出了我在网上看到的有关kafka的文章，有需要的可以看看。
* [基本安装](https://segmentfault.com/a/1190000015765348)
* [apache kafka技术分享系列](https://blog.csdn.net/lizhitao/article/details/39499283)
* [Kafka文件存储机制那些事](https://tech.meituan.com/kafka_fs_design_theory.html)
* [apache kafka系列之server.properties配置文件参数说明](https://blog.csdn.net/lizhitao/article/details/25667831)
#### laravel-kafka 使用说明文档
0. 启动服务
> bin/zookeeper-server-start.sh -daemon config/zookeeper.properties

> bin/kafka-server-start.sh -daemon config/server.properties

kafka 提供了两套 consumer API:
> The high-level Consumer API && The SimpleConsumer API

其中 high-level consumer API 提供了一个从 kafka 消费数据的高层抽象，而 SimpleConsumer API 则需要开发人员更多地关注细节。
`rdkafka` 为此也提供了两套，为什么选择 High Level Consumer。

 > 通常情况下，从kafka读取消息的时候，开发者并不关心消息的offset，而只是想简单的获得数据而已。而High Level Consumer将大部分具体的操作都封装了起来，开发者可以很简单的从kafka读取消息。
  对于High Level Consumer，首先要知道的就是它将每一个分区所对应的offset信息保存在了ZooKeeper中。这个offset所对应的结点名称就是进程在连接到kafka的时候所提供的名称。所以，这个名称也对应了一个Consumer组。
  Consumer的名称是会一直保持在Kafka集群中的，所以，在开始新的代码之前，你必须确保旧的Consumer已经停止了（即进程终止）。如果没有关闭的话，当新的进程，也就是新的Consumer启动的时候，因为两个Consumer的名称相同，所以Kafka会把新进程的消费线程合并到已有的Consumer中，然后触发rebalance（负载均衡）操作。在rebalance中，Kafka会把可用的分区分配给可用的线程，因此，很可能将某些分区分配给了其他的进程。如果你没有关闭旧的进程，导致新旧进程同时存在，那么很有可能一部分的消息会流入到旧的进程中去。
  
1. 生产者生产消息
    //TODO
2. 消费者订阅消息

3. 消费者消费消息
    