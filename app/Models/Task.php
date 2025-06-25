<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'service_name',
        'title',
        'description',
        'location',
        'status',
        'casa',
        'car_brand',
        'car_category',
        'fuel_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
