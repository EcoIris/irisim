<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $table = 'friend';

    protected $primaryKey = 'id';

    public $timestamps = false;
}
