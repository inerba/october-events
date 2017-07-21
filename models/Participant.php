<?php namespace Inerba\Events\Models;

use Model;

/**
 * Model
 */
class Participant extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;
    protected $fillable = ['email', 'name', 'event_id'];

    /*
     * Validation
     */
    public $rules = [
    ];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'inerba_events_participants';

    public $hasOne = [
        'event' => ['Inerba\Events\Models\Event', 'key' => 'id', 'otherKey' => 'event_id']
    ];
}