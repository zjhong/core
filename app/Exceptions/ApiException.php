<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class ApiException extends Exception
{
    protected $code = 500; //default code
    protected $message = '';
    protected $data = [];
    protected $errorFields = [];
    protected $errorLevel = Logger::INFO;

    /**
     * ApiException constructor.
     * @param $code
     * @param string $message
     * @param array $data
     * @param array $errorFields
     * @param int $errorLevel
     */
    public function __construct($code, $message = '', $data = [], $errorFields = [], $errorLevel = Logger::INFO)
    {
        parent::__construct();

        if ($code) {
            $this->code = $code;
        }
        if ($message) {
            $this->message = $message;
        } else {
            $this->message = trans('api.code.' . $this->code);
        }
        if (!empty($data)) {
            $this->data = $data;
        }
        if (!empty($errorFields)) {
            $this->errorFields = $errorFields;
        }

        //set error level
        $this->errorLevel = $errorLevel;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrorFields()
    {
        return $this->errorFields;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJsonResponse()
    {
        $responseArray = [
            'code' => $this->code
        ];
        if ($this->message) {
            $responseArray['message'] = $this->message;
        }
        if (!empty($this->data)) {
            $responseArray['data'] = $this->data;
        }
        if (!empty($this->errorFields)) {
            $responseArray['error_fields'] = $this->errorFields;
        }

        //write log
        Log::log(Logger::getLevelName($this->errorLevel), 'API(' . config('TRACE_CODE') . '): ' . request()->fullUrl() . chr(10) . 'Exception:', $responseArray);

        return response()->json($responseArray);
    }
}
