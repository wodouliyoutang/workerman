<?php

use Workerman\Worker;
require_once '../Workerman/Autoloader.php';
	
	// 创建一个Worker监听2345端口，使用http协议通讯
	$ws_worker = new Worker("websocket://127.0.0.1:2345");
	 
	//启动4个进程对外提供服务 
	$ws_worker->count = 4; 

	//设置Worker子进程启动时的回调函数
	$ws_worker->onWorkerStart = function($worker) {
	    echo "Worker starting...\n";
	};

	$ws_worker->onConnect = function($connection)
	{
	    // var_dump($connection);
	};

   	$ws_worker->onClose = function($connection) {
   		return broadcast("$connection->uid"."-下线了");
   	};


	//当接收到客户端发来的数据后显示数据并回发到客户端  	$data 客户端发送过来的信息
	$ws_worker->onMessage = function($connection, $data) { 
		var_dump($connection); 
	    global $ws_worker;
	    //向客户端回发数据 
	    if (!isset($connection->uid)) {
	    	
	       // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
	       $connection->uid = $data;
	       /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
	        * 实现针对特定uid推送数据
	        */
	       $ws_worker->uidConnections[$connection->uid] = $connection;
	       //return broadcast("$data-上线了");
	    }
	    broadcast($data);

	    // uid 为 all 时是全局广播
	    //全局广播
	    // if ($connection->uid == 'all') {
	    //     broadcast($data);
	    // } else {
	    // // 给特定uid发送
	    //     sendMessageByUid($connection->uid, $data);
	    // }

	};
 
	/**
	 * 向所有验证的用户推送数据
	 * @param  [string] $message [向用户发送的消息内容]
	 * @return [type]          [description]
	 */
	function broadcast($message)
	{
	   global $ws_worker;
	   foreach ($ws_worker->uidConnections as $connection) {
	        $connection->send($message);
	   }
	}
 
	/**
	 * [针对uid推送数据]
	 * @param  [string] $uid     [用户标识符]
	 * @param  [string] $message [向用户发送信息]
	 * @return [type]          [description]
	 */
	function sendMessageByUid($uid, $message)
	{
	    global $ws_worker;
	    if (isset($ws_worker->uidConnections[$uid])) {
	        $connection = $ws_worker->uidConnections[$uid];
	        $connection->send($message);
	    }
	}
 
	// 运行worker
	Worker::runAll();



/* object(Workerman\Connection\TcpConnection)#8 (21) {
  ["onMessage"]=>
  object(Closure)#6 (1) {
    ["parameter"]=>
    array(2) {
      ["$connection"]=>
      string(10) "<required>"
      ["$data"]=>
      string(10) "<required>"
    }
  }
  ["onClose"]=>
  object(Closure)#5 (1) {
    ["parameter"]=>
    array(1) {
      ["$connection"]=>
      string(10) "<required>"
    }
  }
  ["onError"]=>
  NULL
  ["onBufferFull"]=>
  NULL
  ["onBufferDrain"]=>
  NULL
  ["protocol"]=>
  string(30) "\Workerman\Protocols\Websocket"
  ["transport"]=>
  string(3) "tcp"
  ["worker"]=>
  object(Workerman\Worker)#1 (23) {
    ["id"]=>
    int(0)
    ["name"]=>
    string(4) "none"
    ["count"]=>
    int(4)
    ["user"]=>
    string(0) ""
    ["reloadable"]=>
    bool(true)
    ["reusePort"]=>
    bool(false)
    ["onWorkerStart"]=>
    object(Closure)#3 (1) {
      ["parameter"]=>
      array(1) {
        ["$worker"]=>
        string(10) "<required>"
      }
    }
    ["onConnect"]=>
    object(Closure)#4 (1) {
      ["parameter"]=>
      array(1) {
        ["$connection"]=>
        string(10) "<required>"
      }
    }
    ["onMessage"]=>
    object(Closure)#6 (1) {
      ["parameter"]=>
      array(2) {
        ["$connection"]=>
        string(10) "<required>"
        ["$data"]=>
        string(10) "<required>"
      }
    }
    ["onClose"]=>
    object(Closure)#5 (1) {
      ["parameter"]=>
      array(1) {
        ["$connection"]=>
        string(10) "<required>"
      }
    }
    ["onError"]=>
    NULL
    ["onBufferFull"]=>
    NULL
    ["onBufferDrain"]=>
    NULL
    ["onWorkerStop"]=>
    NULL
    ["onWorkerReload"]=>
    NULL
    ["transport"]=>
    string(3) "tcp"
    ["connections"]=>
    array(1) {
      [1]=>
      *RECURSION*
    }
    ["protocol":protected]=>
    string(30) "\Workerman\Protocols\Websocket"
    ["_autoloadRootPath":protected]=>
    string(29) "D:\phpstudy\WWW\wordpress\app"
    ["_mainSocket":protected]=>
    resource(15) of type (stream)
    ["_socketName":protected]=>
    string(26) "websocket://127.0.0.1:2345"
    ["_context":protected]=>
    resource(7) of type (stream-context)
    ["workerId"]=>
    string(32) "000000000235aa4c000000003b41a9bc"
  }
  ["bytesRead"]=>
  int(0)
  ["bytesWritten"]=>
  int(0)
  ["id"]=>
  int(1)
  ["_id":protected]=>
  int(1)
  ["maxSendBufferSize"]=>
  int(1048576)
  ["_socket":protected]=>
  resource(17) of type (stream)
  ["_sendBuffer":protected]=>
  string(0) ""
  ["_recvBuffer":protected]=>
  string(0) ""
  ["_currentPackageLength":protected]=>
  int(0)
  ["_status":protected]=>
  int(2)
  ["_remoteAddress":protected]=>
  string(15) "127.0.0.1:61174"
  ["_isPaused":protected]=>
  bool(false)
  ["_sslHandshakeCompleted":protected]=>
  bool(false)