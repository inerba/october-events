<?php namespace Inerba\Events;

use Event;
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

    function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
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

    }


}
