<?php namespace Inerba\Events;

use Event;
use Inerba\Events\Models\Event as EventModel;
use System\Classes\PluginBase;
use System\Classes\PluginManager;

class Plugin extends PluginBase
{
    /**
     * @var array Plugin dependencies
     */
    public $require = ['RainLab.Blog'];

    public function registerComponents()
    {
    	return [
            'Inerba\Events\Components\NextEvent' => 'NextEvent',
            'Inerba\Events\Components\EventsList' => 'EventsList',
            'Inerba\Events\Components\SingleEvent' => 'SingleEvent',
        ];
    }

    public function registerSettings()
    {
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        \RainLab\Blog\Models\Post::extend(function($model) {
            $model->belongsTo['event'] = ['Inerba\Events\Models\Event', 'key' => 'event_id', 'otherkey' => 'id'];
        });

        Event::listen('backend.form.extendFields', function ($widget) {
            if( PluginManager::instance()->hasPlugin('RainLab.Blog') && $widget->model instanceof \RainLab\Blog\Models\Post)
            {
                $widget->addFields([
                    'event' => [
                        'label'   => 'Evento collegato',
                        'type'    => 'recordfinder',
                        'list' => '$/inerba/events/models/event/columns.yaml',
                        'prompt'    => 'Clicca %s per selezionare un evento',
                        'nameFrom'  => 'name',
                        'tab'     => 'rainlab.blog::lang.post.tab_manage'
                    ]
                ],
                'secondary');
            }
        });

        // Provider di ricerca
        Event::listen('offline.sitesearch.query', function ($query) {

            // Search your plugin's contents
            $items = EventModel::where('name', 'like', "%${query}%")
                                            ->orWhere('description', 'like', "%${query}%")
                                            ->get();

            // Now build a results array
            $results = $items->map(function ($item) use ($query) {

                // Prende la pagina dalla configurazione
                $events_page = \Config::get('inerba.events::events_page');

                // If the query is found in the title, set a relevance of 2
                $relevance = mb_stripos($item->name, $query) !== false ? 2 : 1;

                return [
                    'title'     => $item->name,
                    'text'      => $item->description[0]['content'],
                    'url'       => $events_page ."/". $item->slug,
                    'thumb'     => $item->cover, // Instance of System\Models\File
                    'relevance' => $relevance, // higher relevance results in a higher
                                               // position in the results listing
                    // 'meta' => 'data',       // optional, any other information you want
                                               // to associate with this result
                    // 'model' => $item,       // optional, pass along the original model
                ];
            });

            return [
                'provider' => 'Event', // The badge to display for this result
                'results'  => $results,
            ];
        });

    }


    // NEW TWIG FILTERS
    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'mime' => [$this, 'extractExt'],
                'filesize' => [$this, 'formatSizeUnits']
            ],

        ];
    }

    public function extractExt($filepath)
    {
        $ext = pathinfo($filepath, PATHINFO_EXTENSION);
        return $ext;
    }

    public function formatSizeUnits($size) {
        $mod = 1024;
        $units = explode(' ','B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }
        return round($size, 2) . ' ' . $units[$i];
    }
}
