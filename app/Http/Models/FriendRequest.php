<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    protected $table = 'friend_request';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
