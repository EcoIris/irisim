<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $table = 'group_user';

    protected $primaryKey = 'group_id';

    public $timestamps = false;
}
