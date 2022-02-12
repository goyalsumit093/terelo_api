<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'task';

    protected $fillable = [
        'user_id', 'board_id', 'task_name', 'description', 'task_start_date', 'task_end_date', 'task_final_date', 'status', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User','userid','id');
    }

    public function board()
    {
        return $this->belongsTo('App\Models\Board','board_id','id');
    }
}
