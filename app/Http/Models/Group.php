<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'group';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
