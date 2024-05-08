<?php


namespace App\Services;


use Exception;
use InvalidArgumentException;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    private ?AMQPStreamConnection $connection = null;

    private ?AMQPChannel $channel = null;

    private string $defaultQueueName;

    /**
     * QueueHelper constructor
     */
    public function __construct()
    {
        $this->connect();
    }

    /**
     * On destruct
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Establishes connection to database\
     *
     * @throws InvalidArgumentException
     */
    private function connect(): void
    {
        $rabbitHost = env('RABBITMQ_HOST');
        $rabbitPort = (int)env('RABBITMQ_PORT');
        $rabbitUser = env('RABBITMQ_USER');
        $rabbitPassword = env('RABBITMQ_PASSWORD');
        $rabbitVhost = '/';

        $this->defaultQueueName = env('RABBITMQ_QUEUE_NAME');

        try {
            $this->connection = new AMQPStreamConnection(
                $rabbitHost,
                $rabbitPort,
                $rabbitUser,
                $rabbitPassword,
                $rabbitVhost,
                false,
                'AMQPLAIN',
                null,
                'en_US',
                10.0,
                10.0,
                null,
                true,
                20
            );
            $this->channel = $this->connection->channel();
            $this->channel->basic_qos(0, 100, false);
        } catch (Exception) {
            throw new InvalidArgumentException("Connection error to RabbitMQ queue");
        }
    }

    /**
     * Pushes message to rabbit
     *
     * @param array<string, mixed> $data
     */
    public function pushToQueue(array $data, ?string $queueName = null): void
    {
        if (!$queueName) {
            $queueName = $this->defaultQueueName;
        }

        $this->getChannel()->basic_publish(
            new AMQPMessage(json_encode($data, JSON_THROW_ON_ERROR), ['content_type' => 'application/json']),
            '',
            $queueName,
        );
    }

    /**
     * Returns queue connection
     */
    public function getConnection(): AMQPStreamConnection
    {
        if ($this->connection === null) {
            $this->connect();
        }

        /** @var AMQPStreamConnection $connection */
        $connection = $this->connection;

        return $connection;
    }

    /**
     * Returns queue channel
     */
    public function getChannel(): AMQPChannel
    {
        if ($this->channel === null) {
            $this->connect();
        }

        /** @var AMQPChannel $channel */
        $channel = $this->channel;

        return $channel;
    }

    public function ensureQueueExists(string $queueName): void
    {
        $this->getChannel()->queue_declare($queueName, false, true, false, false);
    }

    /**
     * Closes connection to queue
     */
    public function disconnect(): void
    {
        if ($this->channel !== null) {
            $this->channel->close();
            $this->channel = null;
        }

        if ($this->connection !== null) {
            $this->connection->close();
            $this->connection = null;
        }
    }
}
