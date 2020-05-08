<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>即时通讯IM</title>
    <link rel="stylesheet" href="{{asset('/asset/layui/css/layim.css')}}">
    <link rel="stylesheet" href="{{asset('/asset/layui/css/layui.css')}}">
</head>
<body>
<script type="text/javascript" src="{{asset('/asset/js/jquery.min.js')}}"></script>
<script type="text/javascript" src='{{asset('/asset/js/socket.io.js')}}'></script>
<script type="text/javascript" src="{{asset('/asset/layui/layui.js')}}"></script>
<script type="text/javascript" src="{{asset('/asset/layui/layer.js')}}"></script>
<script>
        layui.use('layim', function(layim){
        var user = {!! $member !!};
        var room = {!! $room !!};
        // 如果服务端不在本机，请把127.0.0.1改成服务端ip
        var socket = io('http://127.0.0.1:3333',{query:{user:JSON.stringify(user),room:JSON.stringify(room)}});

        //基础配置
        layim.config({
            //我的信息、好友列表、群组列表
            init: {
                url: '/chat/getUserRelation', //接口地址
                type: 'get', //默认get，一般可不填
                data: {} //额外参数
            },
            //主面板最小化后显示的名称
            title: user.username,
            //获取群员接口
            members: {
                url: '/chat/getGroupUser', //接口地址
                type: 'get', //默认get，一般可不填
                data: {} //额外参数
            },
            isAudio:true,
            isVideo:true,
            notice:true,
            //上传图片接口
            uploadImage: {
                url: '', //接口地址
                type: 'post' //默认post
            },
            //上传文件接口
            uploadFile: {
                url: '', //接口地址
                type: 'post' //默认post
            },
            msgbox: layui.cache.dir + 'css/modules/layim/html/msgbox.html', //消息盒子页面地址，若不开启，剔除该项即可
            find: layui.cache.dir + 'css/modules/layim/html/find.html', //发现页面地址，若不开启，剔除该项即可
            chatLog: layui.cache.dir + 'css/modules/layim/html/chatlog.html' //聊天记录页面地址，若不开启，剔除该项即可
        });

        // 接收错误提示
        socket.on('message', function (res) {
            layer.msg(res.msg,{icon:2,time:3000});
            return;
        });

        // 更新好友状态
        socket.on('updateStatus', function (res) {
            if (res.status === 'online'){
                layim.setFriendStatus(res.id, 'online');
            }else{
                layim.setFriendStatus(res.id, 'offline');
            }
        });

        // 发送私聊消息
        layim.on('sendMessage', function (res) {
            if (res.to.type === 'group'){
                socket.emit('sendGroupMsg', res);
            }else{
                socket.emit('sendMsg', res);
            }
        });

        // 更新私聊消息
        socket.on('updateMsg', function(res){
            layim.getMessage(res);
        });

        // 更新群组消息
        socket.on('updateGroupMsg', function(res){
            layim.getMessage(res);
        });

        //将好友追加到主面板
        socket.on('updateFriendList', function (res) {
            layim.addList(res);
        });

        socket.on('noticeMsg', function (res) {
            var num = parseInt($('.layim-tool-msgbox').children().text());
            if (num){
                num += 1;
            }else{
                num = 1
            }
            layim.msgbox(num);
        });

        // 更新在线状态
        layim.on('online', function(status){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/chat/updateStatus',
                type: 'POST',
                data: {
                    status: status
                },
                success: function(res) {
                    if (!res.state) {
                        layer.msg(res.message,{icon:2,time:2000});
                        return;
                    }
                    socket.emit('updateStatus', {friends:res.data, status:status, id:user.id});
                }
            });
        });

        // 更新签名
        layim.on('sign', function(value){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/chat/updateSign',
                type: 'POST',
                data: {
                    sign: value
                },
                success: function(res) {
                    if (!res.state) {
                        layer.msg(res.message,{icon:2,time:2000});
                        return;
                    }
                    socket.emit('updateStatus', {friends:res.data, status:status, id:user.id});
                }
            });
        });
    });
</script>
</body>
</html>
