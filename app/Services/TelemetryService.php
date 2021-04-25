<?php

namespace App\Services;

use App\Events\Telemetry;
use App\Models\Assets\Device;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class TelemetryService
{
    /**
     * @param string $data
     */
    public function handle($data)
    {
        $uniqid = uniqid('', false);
        Log::info('telemetry:consumer[' . $uniqid . '][original] ' . $data);

        //hooks
        $obj = new Telemetry($data);

        event($obj);

        Log::info('telemetry:consumer[' . $uniqid . '][parsed] ' . $obj->data);
    }

    /**
     * send data to MQTT client
     * @param string $token
     * @param array $data
     * @throws \Exception
     */
    public function sendDataToClient($token, array $data)
    {
        Log::info('telemetry:sendDataToClient begin', ['token' => $token, 'data' => $data]);

        $exchange = 'amq.topic';
        $topic = 'v1.devices.me.operation';

        $connection = new AMQPStreamConnection(config('servers.rabbitmq.host'), config('servers.rabbitmq.port'), config('servers.rabbitmq.username'), config('servers.rabbitmq.password'));
        $channel = $connection->channel();

        $str = json_encode([
            'token' => $token,
            "values" => $data
        ]);
        $channel->basic_publish(new AMQPMessage($str), $exchange, $topic);

        $channel->close();
        $connection->close();

        Log::info('telemetry:sendDataToClient end');
    }

    /**
     * sendDataToClientByDeviceId
     * @param $device_id
     * @param array $data
     * @throws \Exception
     */
    public function sendDataToClientByDeviceId($device_id, array $data)
    {
        $this->sendDataToClient(Device::getTokenByDeviceId($device_id), $data);
    }

    /**
     * send data to TCP client
     * @param $token
     * @param array $data
     */
    public function sendDataToTCPClient($token, array $data)
    {
        $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        if (!$client->connect('127.0.0.1', 9505, -1)) {
            Log::error("TCP connect failed. {$client->errCode}");
        } else {
            $tcpData = [
                'send_to_client' => 1,
                'token' => $token,
                'values' => $data
            ];
            $client->send(json_encode($tcpData));
            $client->close();
        }
    }

    /**
     * sendDataToTCPClientByDeviceId
     * @param $device_id
     * @param array $data
     */
    public function sendDataToTCPClientByDeviceId($device_id, array $data)
    {
        $this->sendDataToTCPClient(Device::getTokenByDeviceId($device_id), $data);
    }


    //返回当前的毫秒时间戳
    public function microtime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (int)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}

