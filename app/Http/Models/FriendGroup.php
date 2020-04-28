<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FriendGroup extends Model
{
    protected $table = 'friend_group';

    protected $primaryKey = 'id';

    public $timestamps = false;

    public function list()
    {
        return $this->hasMany('App\Http\Models\Friend', 'friend_group_id', 'id');
    }
}
