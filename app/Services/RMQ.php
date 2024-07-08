<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RMQ
{
    protected $connection;
    protected $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('queue.connections.rabbitmq.host'),
            config('queue.connections.rabbitmq.port'),
            config('queue.connections.rabbitmq.user'),
            config('queue.connections.rabbitmq.password'),
            config('queue.connections.rabbitmq.vhost')
        );
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare(
            config('queue.connections.rabbitmq.queue'),
            config('queue.connections.rabbitmq.queue_params.passive'),
            config('queue.connections.rabbitmq.queue_params.durable'),
            config('queue.connections.rabbitmq.queue_params.exclusive'),
            config('queue.connections.rabbitmq.queue_params.auto_delete')
        );
        $this->channel->exchange_declare(
            config('queue.connections.rabbitmq.exchange'),
            config('queue.connections.rabbitmq.exchange_type'),
            config('queue.connections.rabbitmq.exchange_params.passive'),
            config('queue.connections.rabbitmq.exchange_params.durable'),
            config('queue.connections.rabbitmq.exchange_params.auto_delete')
        );
        $this->channel->queue_bind(
            config('queue.connections.rabbitmq.queue'),
            config('queue.connections.rabbitmq.exchange'),
            config('queue.connections.rabbitmq.routing_key')
        );
    }

    public function push($job, $data = '', $queue = null)
    {
        $queue = $queue ?: config('queue.connections.rabbitmq.queue');
        $message = new AMQPMessage(
            json_encode([
                'job' => $job,
                'data' => $data,
            ]),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );
        $this->channel->basic_publish($message, config('queue.connections.rabbitmq.exchange'), $queue);
    }

    public function pop($queue = null)
    {
        $queue = $queue ?: config('queue.connections.rabbitmq.queue');
        $message = $this->channel->basic_get($queue);
        if ($message) {
            $job = json_decode($message->body, true);
            $job['message'] = $message;
            return $job;
        }
        return null;
    }

    public function ack($message)
    {
        $this->channel->basic_ack($message->delivery_info['delivery_tag']);
    }

    public function nack($message)
    {
        $this->channel->basic_nack($message->delivery_info['delivery_tag']);
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}