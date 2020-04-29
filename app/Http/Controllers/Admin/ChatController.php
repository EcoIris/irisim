<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Friend;
use App\Http\Models\FriendGroup;
use App\Http\Models\Group;
use App\Http\Models\GroupUser;
use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    /*
     * 初始化聊天
     * */
    public function chat(Request $request)
    {
        $member = session('member');
        if ($member->status == 'offline'){
            $member->status = 'hide';
        }
        return view('chat.index', [
            'member' => json_encode($member)
        ]);
    }

    /*
     * 更新状态
     * */
    public function updateStatus(Request $request)
    {
        $member = session('member');
        $status = $request->input('status');
        if (!$status){
            return $this->fail('无效参数');
        }
        if ($status == 'hide'){
            $status = 'offline';
        }
        $row = User::where('id', $member->id)->update(['status' => $status]);
        if ($row){
            $member->status = $status;
            session(['member' => $member]);
            $friends = Friend::where('uid', $member->id)->pluck('friend_id')->toArray();
            return $this->success($friends);
        }
        return $this->fail('更新失败');
    }

    /*
     * 更新签名
     * */
    public function updateSign(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'sign' => 'max:30'
        ], [
            'sign.max' => '签名最多30字'
        ]);
        if ($validate->fails()) {
            return $this->fail($validate->errors()->first());
        }
        $member = session('member');
        $sign = $request->input('sign');
        $row = User::where('id', $member->id)->update(['sign' => $sign]);
        if ($row) {
            $member->sign = $sign;
            session(['member' => $member]);
            return $this->success();
        }
        return $this->fail('修改失败');
    }

    /*
     * 获取用户关系
     */
    public function getUserRelation(Request $request)
    {
        $member = session('member');
        if ($member->status == 'offline'){
            $member->status = 'hide';
        }
        $data = [];
        $data['mine'] = $member;
        $data['friend'] = FriendGroup::select('id', 'name as groupname')
            ->with(['list' => function($query){
                return $query->select('friend.friend_group_id', 'user.id', 'user.username', 'user.avatar', 'user.sign', 'user.status')
                    ->leftJoin('user', 'friend.friend_id', '=', 'user.id');
            }])->where('uid', $member->id)
            ->get()
            ->toArray();
        $data['group'] = DB::table('group_user as a')
            ->rightJoin('group as b', 'a.group_id', '=', 'b.id')
            ->select('b.id', 'b.name as groupname', 'b.avatar')
            ->where('a.uid', $member->id)
            ->get()
            ->toArray();
        return response()->json(['code' => 0, 'msg' => '', 'data' => $data]);
    }

    /*
     * 获取群成员
     * */
    public function getGroupUser(Request $request)
    {
        $id = $request->input('id');
        $list = DB::table('group_user as a')
            ->rightJoin('user as b', 'a.uid', '=', 'b.id')
            ->select('b.id', 'b.username', 'b.avatar', 'b.sign')
            ->where('a.group_id', $id)
            ->get()
            ->toArray();
        return response()->json(['code' => 0, 'msg' => '', 'data' => ['list' => $list]]);
    }
}
