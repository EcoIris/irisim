<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * 登录
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function login(Request $request)
    {
        if ($request->ajax()){
            $validate = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required|max:16',
                'captcha' => 'required'
            ], [
                'username.required' => '请输入用户名',
                'password.required' => '请输入密码',
                'password.max' => '密码最长16位',
                'captcha.required' => '请输入验证码'
            ]);
            if ($validate->fails()) {
                return $this->fail($validate->errors()->first());
            }

            $username = $request->input('username');
            $password = $request->input('password');
            $captcha = $request->input('captcha');

            if(!captcha_check(strtoupper($captcha))){
                return $this->fail("验证码错误");
            }

            $user = DB::table('user')
                ->select('id', 'username', 'password', 'avatar', 'sign', 'status')
                ->where('username', $username)
                ->first();
            if (!$user){
                return $this->fail('账号不存在');
            }

            if (!Hash::check($password, $user->password)){
                return $this->fail('密码不正确');
            }

            $row = DB::table('user')
                ->where('id', $user->id)
                ->update(['last_login_time' => date('Y-m-d H:i:s')]);
            if ($row){
                unset($user->password);
                session(['member' => $user]);
                return $this->success('/');
            }
            return $this->fail('登录失败请稍后重试');
        }else{
            return view('login');
        }
    }

    /**
     * 退出登录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        session(['member' => '']);
        return $this->success();
    }
}
