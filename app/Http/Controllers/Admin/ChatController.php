<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\Friend;
use App\Http\Models\FriendGroup;
use App\Http\Models\FriendRequest;
use App\Http\Models\Group;
use App\Http\Models\GroupUser;
use App\Http\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function GuzzleHttp\Psr7\uri_for;

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
        $room = GroupUser::where('uid', $member->id)->pluck('group_id')->toArray();
        return view('chat.index', [
            'member' => json_encode($member, JSON_UNESCAPED_UNICODE),
            'room' => json_encode($room, JSON_UNESCAPED_UNICODE)
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

    /*
     * 查找用户
     * */
    public function searchUser(Request $request)
    {
        $username = $request->input('username');
        $list = User::select('id', 'username', 'avatar')->where('username', 'like', $username)->get()->toArray();
        return $this->success($list);
    }

    /*
     * 添加好友
     * */
    public function addFriend(Request $request)
    {
        $member = session('member');
        $username = $request->input('username');
        $groupId = $request->input('group');
        $remark = $request->input('remark');
        if (!$username || !$groupId){
            return $this->fail('无效参数');
        }

        $uid = User::where('username', $username)->value('id');
        if (!$uid){
            return $this->fail('无效用户');
        }

        $is_friend = Friend::where(['uid' => $member->id, 'friend_id' => $uid])->count('id');
        if ($is_friend){
            return $this->fail($username . '和您已经是好友啦,不可重复添加');
        }

        $add = FriendRequest::where(['from_id' => $member->id, 'to_id' => $uid, 'status' => 1])->count('id');
        if ($add){
            return $this->fail('请勿重复申请添加好友');
        }
        $row = FriendRequest::insert([
            'from_id' => $member->id,
            'to_id' => $uid,
            'friend_group_id' => $groupId,
            'status' => 1,
            'postscript' => $remark,
            'create_time' => date('YmdHis')
        ]);
        if ($row){
            $data = [
                'id' => $uid,
                'code' => 1,
                'msg' => '好友申请'
            ];
            $this->noticeMsg($data);
            return $this->success(['id' => $uid], '好友申请发送成功');
        }
        return $this->fail('好友申请发送失败');
    }

    public function noticeList(Request $request)
    {
        $member = session('member');
        $list = DB::table('friend_request as a')
            ->leftJoin('user as b', 'a.from_id', '=', 'b.id')
            ->select('a.id', 'a.from_id as uid', 'a.friend_group_id as from_group', 'a.to_id', 'a.status', 'a.postscript as remark', 'a.create_time as time', 'b.id as user_id', 'b.username', 'b.avatar', 'b.sign')
            ->where('to_id', $member->id)
            ->orderBy('id', 'desc')
            ->paginate(10);
        if ($list->items()){
            foreach ($list->items() as $value){
                $value->href = null;
                $value->read = 1;
                $value->type = 1;
                if ($value->status == 1){
                    $value->content = '申请添加你为好友';
                    $value->from = 1;
                }elseif ($value->status == 2){
                    $value->content = '你同意了 ' . $value->username . ' 的好友申请';
                    $value->from = null;
                }else{
                    $value->content = '你拒绝了 ' . $value->username . ' 的好友申请';
                    $value->from = null;
                }
                $value->user = [
                    "id" => $value->user_id,
                    "avatar" => $value->avatar,
                    "username" => $value->username,
                    "sign" => $value->sign
                ];
            }
        }
        return $this->success($list->items());
    }

    /*
     * 同意好友申请
     * */
    public function agreeFriend(Request $request)
    {
        try {
            DB::beginTransaction();
            $member = session('member');
            $uid = $request->input('uid');
            $from_group = $request->input('from_group');
            $group = $request->input('group');
            $time = date('YmdHis');
            $is_friend = Friend::where(['uid' => $member->id, 'friend_id' => $uid])->count('id');
            if ($is_friend){
                DB::commit();
                return $this->success();
            }
            $friendRequest = FriendRequest::where(['from_id' => $uid, 'to_id' => $member->id])->first();
            $friendRequest->status = 2;
            $friendRequest->update_time = $time;
            $friendRequest->save();
            $data = [
                [
                    'uid' => $member->id,
                    'friend_id' => $uid,
                    'friend_group_id' => $group,
                ],
                [
                    'uid' => $uid,
                    'friend_id' => $member->id,
                    'friend_group_id' => $from_group,
                ]
            ];
            $row = Friend::insert($data);
            if ($row){
                $data = [
                    'id' => $member->id,
                    'uid' => $uid,
                    'username' => $member->username,
                    'avatar' => $member->avatar,
                    'sign' => $member->sign,
                    'groupid' => $from_group,
                    'type' => 'friend',
                    'code' => 2
                ];
                $this->noticeMsg($data);
                DB::commit();
                return $this->success();
            }
            throw new \Exception('添加失败');
        }catch (\Exception $e){
            DB::rollBack();
            return $this->fail($e->getMessage());
        }
    }

    /*
     * 拒绝好友申请
     * */
    public function refuseFriend(Request $request)
    {
        $member = session('member');
        $uid = $request->input('uid');
        $time = date('YmdHis');
        $friendRequest = FriendRequest::where(['from_id' => $uid, 'to_id' => $member->id])->first();
        $friendRequest->status = 3;
        $friendRequest->update_time = $time;
        $friendRequest->save();
        return $this->success();
    }
}
