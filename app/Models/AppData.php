<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppData extends Model
{
    protected $table = 'app_data';

    protected $fillable = ['data_key', 'value'];
}
