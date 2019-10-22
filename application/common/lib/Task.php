<?php
/**
 * Created by PhpStorm.
 * User: nange
 * Date: 2019/10/15
 * Time: 0:28
 */

namespace app\common\lib;

class Task
{
    use ReturnJson;
    public function SMsg($data, $serv) {
        try {
            // 调用发短信接口
            $res = SendSms::sendCode($data['phoneNum'], $data['code']);
        } catch (\Exception $e) {
           return $this->retFail($e->getMessage());
        }

        // 存入redis
        if ($redis = Redis::getInstance()) {
            $num = json_decode($data['code']);
            $key = 'ng_'.$data['phoneNum'] . $num->code;

            $res = $redis->setVal($key, $num->code, 120);

            if (!$res) {
                return $this->retFail('存入失败');
            }
        } else {
            return $this->retFail('redis链接失败');
        }

        return $this->retSucc('存入code成功');

    }

    // 想客户端发送消息
    public function sendMsg($data, $serv) {
        $requestArr = Redis::getInstance()->sMembers('request_id');
        foreach ($requestArr as $fd) {
            $serv->push($fd, json_encode($data));
//            $serv->push($fd, '胡莎莎');
        }
    }
}