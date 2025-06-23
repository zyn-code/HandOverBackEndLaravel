<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'contractor_id',
        'price',
        'comments',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
