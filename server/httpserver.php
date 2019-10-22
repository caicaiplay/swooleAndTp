<?php
/**
 * swoole_http_server
 *
 */

use Swoole\Http\Request;
use Swoole\Http\Response;
use app\common\lib\Task;

class Http
{
    // 监听服务器id
    const HOST = '0.0.0.0';

    // 端口
    const PORT = 8811;

    // set的数组
    const CONF = array(
        'enable_static_handler' => true,
        'document_root' => '/media/sf_vboxshare/think/public/static/', // v4.4.0以下版本, 此处必须为绝对路径
        'worker_num' => 4,
        'task_worker_num' => 4,
        'max_request'     => 4,
    );

    // serv资源
    private $http = null;

    /**
     * 初始化set, on..函数
     * Http constructor.
     */
    public function __construct()
    {
        // 实例化一个http服务器
        $this->http = new Swoole\Http\Server(self::HOST, self::PORT);
        // 服务器配置
        $this->http->set(self::CONF);
        // worker进程前初始化一些文件
        $this->http->on('WorkerStart', [$this, 'onWorkerStart']);
        // 收到请求执行
        $this->http->on('request', [$this, 'onRequest']);
        // 开启Task协程
        $this->http->on('Task', [$this, 'onTask']);
        // task完成时候执行
        $this->http->on('Finish', [$this, 'onFinish']);
        // 关闭进程
        $this->http->on("close", [$this, 'onClose']);

        // 开启http服务器监听
        $this->http->start();
    }

    /**
     * onworkerStart函数, 加载文件到公共内存,  在次初始化的文件进程之间共享
     * @param $serv
     * @param $worker_id
     */
    public function onWorkerStart($serv, $worker_id)
    {
        require __DIR__ . '/../thinkphp/base.php';
        require __DIR__ . '/../vendor/autoload.php';
    }

    /**
     * onrequest函数把转化城原生$_GET, $_POST
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(Request $request, Response $response)
    {
//        $_SERVER  =  [];
        if (isset($request->server)) {
            foreach ($request->server as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }


        if (isset($request->header)) {
            foreach ($request->header as $k => $v) {
                $_SERVER[strtoupper($k)] = $v;
            }
        }

//        $_GET = [];
        if (isset($request->get)) {
            foreach ($request->get as $k => $v) {
                $_GET[$k] = $v;
            }
        }

//        $_POST = [];
        if (isset($request->post)) {
            foreach ($request->post as $k => $v) {
                $_POST[$k] = $v;
            }
        }

        if (isset($request->files)) {
            foreach ($request->post as $k => $v) {
                $_FILES[$k] =$v;
            }
        }

        $this->writeLog();

        $_POST['http_server'] = $this->http;

        // 输入缓存中
        ob_start();
        // 执行应用并响应
        try {
            \think\Container::get('app')->run()->send();
        } catch (\Exception $e) {
//            echo json_encode($e->getMessage());
        }
        $res = ob_get_contents();
        ob_end_clean();

        // 返回客户端内容
        $response->end($res);
        $this->http->close($request->fd);
    }
    // 写日志
    public  function writeLog() {
        $arr = array_merge(['date' => date()],$_SERVER, $_POST, $_GET, $_FILES);

        $logs = '';
        foreach ($arr as $k => $v) {
            $logs .= $k. ':' . $v;
        }

        swoole_async_writefile(__DIR__.'/log/'.date('Ym').'/'.date('d').'/swol.log',
            $logs, function($filename) {

        }, FILE_APPEND);


    }

    // onTask
    public function onTask($serv, $task_id, $from_id, $data) {
        if (!isset($data['method'])) {
            echo  'Task 任务没有设置';
            return '';
        }

        $sendCode = new Task();

        $method = $data['method'];

        $res = $sendCode->$method($data['data']);
        echo $res;
    }
    // onfinish
    public function onFinish($serv, $task_id, $data) {

    }

    /**
     * close
     * @param $ws
     * @param $fd
     */
    public function onClose($ws, $fd)
    {
        echo "clientid:{$fd}\n";
    }
}

// 实例化http
$server = new Http();

