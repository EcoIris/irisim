<!DOCTYPE>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>用户登录</title>
    <meta name="keywords" content="web端,即时通讯,socket,PHPSocketIO,IM">
    <meta name="description" content="基于PHPSocketIO的web端即时通讯IM">
    <link href="{{asset('/asset/layui/css/login.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('/asset/layui/css/H-ui.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('/asset/layui/css/iconfont.css')}}" rel="stylesheet" type="text/css" />
</head>
<body>
<style>
    .footer a {
        color: #fff;
    }
</style>
<input type="hidden" id="TenantId" name="TenantId" value="" />
<div class="loginWraper">
  <div id="loginform" class="loginBox">
    <form id="login" class="form form-horizontal" >
      <div class="row cl">
          <label class="form-label col-xs-3"></label>
          <div class="formControls col-xs-8">
          <input name="username" type="text" placeholder="请输入用户名" autocomplete="off" class="input-text size-L" required>
        </div>
      </div>
      <div class="row cl">
          <label class="form-label col-xs-3"></label>
          <div class="formControls col-xs-8">
          <input name="password" type="password" placeholder="请输入密码" autocomplete="off" class="input-text size-L" required>
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"></label>
        <div class="formControls col-xs-8">
          <input name="captcha" class="input-text size-L" type="text" placeholder="验证码" style="width:150px;">
          <img class="captcha" src="{{captcha_src('flat')}}" title="点击图片重新获取验证码">
          <a id="captcha" href="javascript:;">点我换一张</a>
        </div>
      </div>
      <div class="row cl">
        <label class="form-label col-xs-3"></label>
        <div class="formControls col-xs-8">
          <input name="save" type="button" class="btn btn-secondary radius size-L" value="&nbsp;登&nbsp;&nbsp;&nbsp;&nbsp;录&nbsp;">
          <input type="reset" class="btn btn-default radius size-L" value="&nbsp;取&nbsp;&nbsp;&nbsp;&nbsp;消&nbsp;">
        </div>
      </div>
    </form>
  </div>
</div>
<div class="footer">Copyright IM即时通讯<a href="http://www.beian.miit.gov.cn">xICP备xxxxxx号</a></div>
<script type="text/javascript" src="{{asset('/asset/js/jquery.min.js')}}"></script>
<script src="{{asset('/asset/layui/layer.js')}}"></script>
<script>

  $(document).keydown(function(event){
    if(event.keyCode == 13){
      logIn();
    }
  });

  $('.captcha').on('click', function () {
    $(this).attr('src', $(this).attr('src') + '?v=' + Math.random());
  });

  $('#captcha').on('click', function () {
    $('.captcha').attr('src', $('.captcha').attr('src') + '?v=' + Math.random());
  });

  var login = $('#login');

  login.find('input[name=save]').on('click', function () {
    logIn();
  });

  function logIn() {
    var username = login.find('input[name=username]');
    if (username.val() === '') {
      layer.msg('请填写手机号',{icon:5,time:1000});
      username.focus();
      return;
    }
    var password = login.find('input[name=password]');
    if (password.val() === '') {
      layer.msg('请填写密码',{icon:5,time:1000});
      password.focus();
      return;
    }
    var code = login.find('input[name=captcha]');
    if (code.val() === '') {
      layer.msg('请填写验证码',{icon:5,time:1000});
      return;
    }
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: '/login',
      type: 'POST',
      data: new FormData($('#login')[0]),
      processData: false,
      contentType: false,
      success: function(res) {
        if (!res.state) {
          layer.msg(res.message,{icon:2,time:2000});
          $('.captcha').attr('src', $('.captcha').attr('src') + '?v=' + Math.random());
          return;
        }
        location.replace(res.data);
      }
    });
  }
</script>
</body>
</html>
