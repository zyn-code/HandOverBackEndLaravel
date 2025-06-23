<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'service_categories',
        'location',
        'years_of_experience',
        'description',
    ];

    protected $casts = [
        'service_categories' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function offers()
    {
        return $this->hasMany(Offer::class);
    }
}
