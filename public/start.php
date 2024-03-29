<?php
require '../vendor/autoload.php';
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use App\Http\Controllers\Controller;

// 已连接的用户列表
$user = [];
// 创建socket.io服务端，
$io = new SocketIO(3333);

$io->on('workerStart', function($socket) use ($io){
    // 监听一个text 3121端口
    $inner_http_worker = new Worker('text://0.0.0.0:3121');
    $inner_http_worker->onMessage = function($http_connection, $data) use ($io){
        global $user;
        $data = json_decode($data,true);
        if ($data['code'] == 1){
            $io->to($user[$data['uid']]['socketId'])->emit('noticeMsg');
        }elseif ($data['code'] == 2){
            $io->to($user[$data['uid']]['socketId'])->emit('updateFriendList', $data);
        }elseif ($data['code'] == 3){
            if (isset($user[$data['uid']])) {
                $io->to($user[$data['uid']]['socketId'])->emit('msgBox', $data['read']);
            }
        }elseif ($data['code'] == 4){
            foreach ($data['uid'] as $value){
                $io->to($user[$value]['socketId'])->emit('removeGroup', $data['group']);
            }
        }elseif ($data['code'] == 5){
            $http_connection->leave('room'.$data['group']);
            $io->to($user[$data['uid']]['socketId'])->emit('removeGroup', $data['group']);
        }elseif ($data['code'] == 6){
            $io->to($user[$data['uid']]['socketId'])->emit('removeFriend', $data['friendId']);
        }
    };
    // 执行监听
    $inner_http_worker->listen();
});

// 客户端连接
$io->on('connection', function($socket) use ($io){

    global $user;
    $param = $socket->handshake['query'];
    if (!isset($param['user'])){
        $socket->send(['errorCode' => 1, 'msg' => '无效用户']);
        $socket->disconnect();
        return;
    }
    $member = json_decode($param['user'], true);
    $member['socketId'] = $socket->id;
    $user[$member['id']] = $member;

    // 创建群聊房间
    if ($param['room']){
        $param['room'] = json_decode($param['room'], true);
        foreach ($param['room'] as $value){
            $socket->join('room' . $value);
        }
    }

    // 发送私聊消息
    $socket->on('sendMsg', function($data) use ($socket){
        global $user;
        $res = [
            'id' => $data['mine']['id'], //消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
            'username' => $data['mine']['username'], //消息来源用户名
            'avatar' => $data['mine']['avatar'], //消息来源用户头像
            'type' => $data['to']['type'], //聊天窗口来源类型，从发送消息传递的to里面获取
            'content' => $data['mine']['content'], //消息内容
            'cid' => 0, //消息id，可不传。除非你要对消息进行一些操作（如撤回）
            'mine' => false, //是否我发送的消息，如果为true，则会显示在右方,
            'fromid' => $data['mine']['id'], //消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
            'timestamp' => Controller::getMillisecond() //服务端时间戳毫秒数。注意：如果你返回的是标准的 unix 时间戳，记得要 *1000,
        ];
        if (isset($user[$data['to']['id']])){
            $socket->to($user[$data['to']['id']]['socketId'])->emit('updateMsg', $res);
        }
    });

    // 发送群消息
    $socket->on('sendGroupMsg', function ($data) use ($socket){
        $res = [
            'id' => $data['to']['id'], //消息的来源ID（如果是私聊，则是用户id，如果是群聊，则是群组id）
            'username' => $data['mine']['username'], //消息来源用户名
            'avatar' => $data['mine']['avatar'], //消息来源用户头像
            'type' => $data['to']['type'], //聊天窗口来源类型，从发送消息传递的to里面获取
            'content' => $data['mine']['content'], //消息内容
            'cid' => 0, //消息id，可不传。除非你要对消息进行一些操作（如撤回）
            'mine' => false, //是否我发送的消息，如果为true，则会显示在右方,
            'fromid' => $data['mine']['id'], //消息的发送者id（比如群组中的某个消息发送者），可用于自动解决浏览器多窗口时的一些问题
            'timestamp' => Controller::getMillisecond() //服务端时间戳毫秒数。注意：如果你返回的是标准的 unix 时间戳，记得要 *1000,
        ];
        $socket->to('room' . $data['to']['id'])->emit('updateGroupMsg', $res);
    });

    // 更新好友状态
    $socket->on('updateStatus', function ($data) use ($io){
        global $user;
        $keys = array_keys($user);
        foreach ($data['friends'] as $value){
            if (in_array($value, $keys)){
                $io->to($user[$value]['socketId'])->emit('updateStatus', ['id' => $data['id'], 'status' => $data['status']]);
            }
        }
    });

    // 断开连接更新用户列表
    $socket->on('disconnect', function () use($socket) {
        global $user;
        foreach ($user as $key => $value){
            if ($value['socketId'] == $socket->id){
                unset($user[$key]);
            }
        }
    });
});

Worker::runAll();
