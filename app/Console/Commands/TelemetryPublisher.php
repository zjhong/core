<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Test publisher
 * Class TelemetryPublisher
 * @package App\Console\Commands
 */
class TelemetryPublisher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telemetry:publisher {--token=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'telemetry publish rabbitMQ';

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
        $this->info('Starting telemetry:publisher');

        try {
            \Co\run(function () {
                $exchange = 'amq.topic';
                $topic = 'v1.devices.me.telemetry';

                $connection = new AMQPStreamConnection(config('servers.rabbitmq.host'), config('servers.rabbitmq.port'), config('servers.rabbitmq.username'), config('servers.rabbitmq.password'));
                $channel = $connection->channel();

                while (true) {
                    sleep(1);
                    $str = json_encode([
                        'token' => $this->option('token'),
                        'ts' => \App\Facades\Telemetry::microtime(),
                        "values" => [
                            "temp" => rand(25, 35),
                            "hum" => rand(70, 90),
                        ]
                    ]);
                    $channel->basic_publish(new AMQPMessage($str), $exchange, $topic);
                }

                $channel->close();
                $connection->close();

            });

        } catch (\Exception $e) {
            Log::error('telemetry:publisher', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

}
