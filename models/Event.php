<?php namespace Inerba\Events\Models;

use Model;
use Carbon\Carbon;
use Inerba\Embedd\Classes\Embedd;

/**
 * Model
 */
class Event extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    
    /*
     * Validation
     */
    public $rules = [
    ];

    // Crea lo slug a partire dal titolo dell'evento
    protected $slugs = ['slug' => 'name'];

    protected $jsonable = ['description', 'address', 'media', 'setup'];

    // Copertina dell'evento
    public $attachOne = [
        'cover' => 'System\Models\File'
    ];

    // Fotogallery evento e allegati
    public $attachMany = [
        'gallery' => 'System\Models\File',
        'attachments' => 'System\Models\File'
    ];

    // Post del blog collegati all'evento
    public $hasMany = [
        'posts' => ['RainLab\Blog\Models\Post', 'key' => 'event_id', 'otherKey' => 'id']
    ];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'inerba_events_';

    public function beforeSave()
    {

        $Embedd = new Embedd();

        foreach ($this->media as $media) {
            $Media = $Embedd->retrieve($media['url']);

            $embed[] = $Media;
        }
        $this->media = isset($embed) ? $embed : null;

        foreach ($this->description as $media_desc) {

            $e_desc[] = [
                'title' => $media_desc['title'],
                'content' => $media_desc['content'],
                'embed' => $Embedd->retrieve($media_desc['embed']), 
            ];
        }

        // dd($e_desc);

        $this->description = $e_desc;

    }

    public function setUrl($pageName,$controller)
    {
        $params = [
            'id'   => $this->id,
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params);
    }

    public function getIsExpiredAttribute()
    {
        return $this->start < Carbon::now();
    }

    public function scopeIsNotExpired($query)
    {
        return $query
            ->whereNotNull('start')
            ->where('start', '>', Carbon::now())
        ;
    }

    public function scopeIsExpired($query)
    {
        return $query
            ->whereNotNull('start')
            ->where('start', '<', Carbon::now())
        ;
    }
}