<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $softDelete = true;

    protected $fillable = [
        'board_name', 'board_start_at', 'board_end_at', 'board_final_date', 'board_description', 'detail', 'created_by'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','created_by');
    }
}
