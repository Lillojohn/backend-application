<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RosterEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roster_events';

    /**
     * The default attributes.
     *
     * @var array
     */
    protected $attributes = [
        'date' => null,
        'activity' => null,
        'from' => null,
        'to' => null,
        'arrival_time' => null,
        'departure_time' => null,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date', 'activity', 'from', 'to', 'arrival_time', 'departure_time',
    ];

    /**
     * Get the ExtraActivities for the RosterEvent.
     */
    public function extraActivities()
    {
        return $this->hasMany(ExtraActivities::class);
    }
}
