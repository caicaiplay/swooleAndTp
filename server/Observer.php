<?php
/**
 * Created by PhpStorm.
 * User: nange
 * Date: 2019/10/16
 * Time: 17:42
 */

class Observer
{
    const PORT = 8811;

    public function port() {
        $shell = 'netstat -anp 2>/dev/null | grep '.self::PORT . ' |grep LISTEN | wc -l';

       $flag = shell_exec($shell);

       if ($flag != 1) {
           // 发送短信;
           echo 'error'.PHP_EOL;
       }
//       echo $flag;
    }
}

// swoole 定时器 每2秒执行一次shell
swoole_timer_tick(2000, function ($timer_id) {
    (new  Observer())->port();
    echo 'swoole_timer_tick'.PHP_EOL;
});

// 后台运行脚本
// nohup  /php/bin/php /home/ser/serv.php  > /log.log &
// sigusr1  usr2



