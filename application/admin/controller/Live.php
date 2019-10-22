<?php

namespace app\admin\controller;

use app\common\lib\ReturnJson;
use think\Controller;

class Live extends Controller
{
    use ReturnJson;
    //
    public function index() {

        return view('admin@live/live');
    }

    public function upload() {
        // 获取表单上传文件 例如上传了001.jpg
//        $file = request()->file('image');
//        $_POST['http_server']->push(3, 'sdasdasd');
        $server = $_POST['http_server'];
        foreach ($server->connections as $fd) {
            $server->send($fd, 'heiio, skndk');
        }

        return 111;
    }

    public function say() {
        $arr = request()->get();
        $data = [
            'method' => 'sendMsg',
            'data' => $arr
        ];
        $_POST['http_server']->task($data);

        return $this->retSucc('发送成功');

    }
}
