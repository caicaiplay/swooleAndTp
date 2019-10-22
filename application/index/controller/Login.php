<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use app\common\lib\ReturnJson;
use app\common\lib\Redis;

class Login extends Controller
{
    // 使用trait类返回json
    use ReturnJson;
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function login()
    {
        return view('index@login/login');
    }

    /**
     * 获取验证码
     *
     * @return \think\Response
     */
    public function getCode()
    {
        // 接收数据
        $data = request()->route();

        // 验证器验证
        $result = $this->validate($data,'app\common\validate\PhoneNum.pNum');

        if(true !== $result){
            $res = $this->retFail($result);

            // 返回json错误信息
            return $res;
        }

        $num = rand(1000, 9999);

        $code =json_encode(array(
            'code' => $num
        ));

        $taskData = [
            'method' => 'SMsg',
            'data' => [
                'phoneNum' => $data['phoneNum'],
                'code' => $code
            ]
        ];

        $_POST['http_server']->task($taskData);

        /**
         * 异步redis
         */
        /*
        $sRedis = new \Swoole\Coroutine\Redis();
        $sRedis->connect(config('redis.hostname'),
            config('redis.port'));
        $key = config('redis.suffix').$data['phoneNum'] . $num;
        $sRedis->set($key, $num, 120);
        */

       return $this->retSucc('获取成功');
    }

    /**
     * 注册用户
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function register(Request $request)
    {
        // 解析传过来的数据
//        parse_str($request->post('data'), $arr);
        $arr = $_GET;

        // 验证提交的数据
        $res = $this->validate($arr, 'app\common\validate\PhoneNum.register');

        // 验证是否通过
        if(true !== $res){
            // 返回json错误信息
            return $this->retFail($res);
        }

        $redis = Redis::getInstance();
        if (!$redis) {
            return $this->retFail('redis connect fail');
        }
        $key = config('redis.suffix').$arr['phoneNum'] . $arr['code'];
        $data =  $redis->getVal($key);
        if ($data  != $arr['code']) {
            return $this->retFail('验证不通过');
        }

        $redis->setVal('us_'.$arr['phoneNum'], md5($arr['phoneNum']),3600);

        return $this->retSucc('验证成功');
    }

}
