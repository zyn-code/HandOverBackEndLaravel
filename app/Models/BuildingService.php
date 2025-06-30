<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'building_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'service_name',
        'icon', 
    ];
    protected $appends = [
        'icon_url',         
    ];
    public function getIconUrlAttribute(): string
    {
        return asset('Icons/' . $this->icon);  
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'service_id');
    }
}
