<?php

namespace App\Services;

use App\Facades\Extension;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\Timer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * Web socket
 */
class WebSocketService implements WebSocketHandlerInterface
{
    /**@var \Swoole\Table $wsTable */
    private $wsTable;

    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
    }

    /**
     * connection
     * @param Server $server
     * @param Request $request
     */
    public function onOpen(Server $server, Request $request)
    {
        /**
         * Auth check
         */
        $user = Auth::user();
        if ($user) {
            $this->wsTable->set('uid:' . $user->id, ['value' => $request->fd, 'temp' => '']);// bind uid to fd
            $this->wsTable->set('fd:' . $request->fd, ['value' => $user->id, 'temp' => '']);// bind fd to uid
        } else {
            $server->push($request->fd, $this->gzip("Auth failed #{$request->fd}"));
            $server->close($request->fd);
        }

        Log::info('New WebSocket connection', [$request->fd, request()->all(), session()->getId(), session('xxx'), session(['yyy' => time()])]);

        $server->push($request->fd, $this->gzip("Welcome to ThingsPanel #{$request->fd}"));
    }

    /**
     * Chart request
     * @param Server $server
     * @param Frame $frame
     */
    public function onMessage(Server $server, Frame $frame)
    {
        Log::info('onMessage Received', [$frame->fd, $frame->data, $frame->opcode, $frame->finish]);

        try {
            $data = json_decode($frame->data, true);

            //push init data
            $server->push($frame->fd, $this->gzip(Extension::handle($data, true)));

            //clean timer
            $this->cleanTimer($frame->fd, $data['wid']);

            //get interval time, seconds, Send data cyclically
            if (isset($data['config']['interval']) && $data['config']['interval'] >= 500) {
                $millisecond = $data['config']['interval'];

                //push telemetry data
                $timerID = Timer::tick($millisecond, function () use ($server, $frame, $data) {
                    $returnData = json_decode(Extension::handle($data), true);
                    if (!$server->push($frame->fd, $this->gzip(json_encode($returnData)))) {
                        Log::error('send Message failed', [$data, $returnData]);
                    }
                });

                $this->wsTable->set('fdt:' . $frame->fd . ':' . $data['wid'], ['value' => $timerID, 'temp' => '']);// 绑定fd到uid的映射

                //store all timer id
                $tid = $this->wsTable->get('tid:' . $frame->fd);
                if ($tid !== false) {
                    $temp = explode(',', $tid['temp']);
                    if (!in_array($timerID, $temp)) {
                        array_push($temp, $timerID);
                    }
                } else {
                    $temp = [$timerID];
                }
                $this->wsTable->set('tid:' . $frame->fd, ['value' => 0, 'temp' => implode(',', $temp)]);
            }
        } catch (\Exception $e) {
            Log::info('onMessage Error', [$frame->fd, $e->getMessage(), $e->getTraceAsString()]);
            $server->push($frame->fd, $this->gzip('[' . date('Y-m-d H:i:s') . '] ERROR: ' . $e->getMessage()));
        }
    }

    /**
     * @param Server $server
     * @param $fd
     * @param $reactorId
     */
    public function onClose(Server $server, $fd, $reactorId)
    {
        //clear all fd's timer
        $tid = $this->wsTable->get('tid:' . $fd);
        if ($tid !== false) {
            $temp = explode(',', $tid['temp']);
            if (!empty($temp)) {
                foreach ($temp as $timerID) {
                    Timer::clear($timerID);
                }
            }
            $this->wsTable->del('tid:' . $fd);
        }

        $uid = $this->wsTable->get('fd:' . $fd);
        if ($uid !== false) {
            $this->wsTable->del('uid:' . $uid['value']); // unbind uid
        }
        $this->wsTable->del('fd:' . $fd);// unbind fd
        $server->push($fd, $this->gzip("Goodbye #{$fd}"));

        Log::info('Websocket onClose', [$fd]);
    }

    /**
     * Clean timer
     * @param $fd
     * @param $wid
     */
    private function cleanTimer($fd, $wid)
    {
        $timerID = $this->wsTable->get('fdt:' . $fd . ':' . $wid);
        if ($timerID !== false) {
            Timer::clear($timerID['value']);
            //clear temp
            $tid = $this->wsTable->get('tid:' . $fd);
            if ($tid !== false) {
                $temp = explode(',', $tid['temp']);
                if (!empty($temp)) {
                    $temp = array_diff($temp, [$timerID['value']]);
                    $this->wsTable->set('tid:' . $fd, ['value' => 0, 'temp' => implode(',', $temp)]);
                }
            }
        }
        $this->wsTable->del('fdt:' . $fd . ':' . $wid);// unbind fd timer
    }

    /**
     * @param $message
     * @return string
     */
    private function gzip($message)
    {
        return $message;
        //return TODO base64_encode(gzencode($message, 6));
    }

}
