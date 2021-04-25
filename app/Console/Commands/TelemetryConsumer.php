<?php

namespace App\Console\Commands;

use App\Facades\Telemetry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class TelemetryConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemetry:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'telemetry consumer rabbitMQ';

    protected $queue = 'thingspanel';
    protected $exchange = 'amq.topic';
    protected $topic = 'v1.devices.me.telemetry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @throws \ErrorException
     */
    public function handle()
    {
        \Swoole\Runtime::enableCoroutine();
        $this->info(date('Y-m-d H:i:s') . ' Starting telemetry:consumer');

        try {
            \Co\run(function () {

                $connection = new AMQPStreamConnection(config('servers.rabbitmq.host'), config('servers.rabbitmq.port'), config('servers.rabbitmq.username'), config('servers.rabbitmq.password'));
                $channel = $connection->channel();

                $channel->basic_qos(0, 1000, false);

                /*
                    name: $queue
                    passive: false
                    durable: true // the queue will survive server restarts
                    exclusive: false // the queue can be accessed in other channels
                    auto_delete: false //the queue won't be deleted once the channel is closed.
                */
                $channel->queue_declare($this->queue, false, true, false, false);

                /*
                    name: $exchange
                    type: direct
                    passive: false
                    durable: true // the exchange will survive server restarts
                    auto_delete: false //the exchange won't be deleted once the channel is closed.
                */
                //$channel->exchange_declare($exchange, AMQPExchangeType::TOPIC, false, true, false);

                $channel->queue_bind($this->queue, $this->exchange, $this->topic);

                $callback = function ($message) {
                    Telemetry::handle($message->body);
                };

                $channel->basic_consume($this->queue, '', false, true, false, false, $callback);

                /**
                 * @param \PhpAmqpLib\Channel\AMQPChannel $channel
                 * @param \PhpAmqpLib\Connection\AbstractConnection $connection
                 */
                $shutdown = function ($channel, $connection) {
                    $channel->close();
                    $connection->close();
                };

                register_shutdown_function($shutdown, $channel, $connection);

                while ($channel->is_consuming()) {
                    $channel->wait();
                }
            });

        } catch (\Exception $e) {
            Log::error('telemetry:consumer', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        $this->info(date('Y-m-d H:i:s') . ' Stop telemetry:consumer');
    }

}
