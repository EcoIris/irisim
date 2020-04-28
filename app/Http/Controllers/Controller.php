<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 请求成功
     * @param string $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($data = '', $message = '') {
        if (empty($data)){
            $data = new \stdClass();
        }
        return response()->json([
            'state' => 1,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 请求失败
     * @param string $message
     * @param string $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function fail($message = '', $data = '') {
        if (empty($data)){
            $data = new \stdClass();
        }
        return response()->json([
            'state' => 0,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * 获取毫秒级时间戳
     * */
    static public function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+$t2)*1000);
    }
}
