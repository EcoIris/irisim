<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Models\FriendGroup;
use App\Http\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $member = session('member');
        $data = [];
        $data['mine'] = $member;
        $data['mine']->status = 'online';
        $data['friend'] = FriendGroup::select('id', 'name as groupname')
            ->with(['list' => function($query){
                return $query->select('friend.friend_group_id', 'user.id', 'user.username', 'user.avatar', 'user.sign', 'user.status')
                    ->leftJoin('user', 'friend.friend_id', '=', 'user.id');
            }])->where('uid', $member->id)
            ->get()
            ->toArray();
        $data['group'] = [];
        return view('chat.index', [
            'member' => json_encode($member),
            'list' => json_encode($data, JSON_UNESCAPED_UNICODE)
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
        $row = User::where('id', $member->id)->update(['status' => $status]);
        if ($row){
            return $this->success();
        }
        return $this->fail('更新失败');
    }
}
