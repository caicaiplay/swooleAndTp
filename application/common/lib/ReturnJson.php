<?php
/**
 * Created by PhpStorm.
 * User: nange
 * Date: 2019/10/14
 * Time: 14:39
 */

namespace app\common\lib;

// 返回json数据类
trait ReturnJson
{
    public function retSucc($data='', $msg = 'success', $code = 1)
    {
        return  json_encode(array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ));
    }

    public function retFail($data='', $msg = 'fail', $code = 0)
    {
        return  json_encode(array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ));
    }
}