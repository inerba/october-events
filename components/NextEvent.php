<?php namespace Inerba\Events\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Inerba\Events\Models\Event;

class NextEvent extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Prossimo Evento',
            'description' => 'Visualizza l\'evento più vicino'
        ];
    }

    public function defineProperties()
    {
        return [
            'listPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_category',
                'description' => 'rainlab.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'Links',
            ],
            'eventPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
        ];
    }

    public function getListPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getEventPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {

        $event = Event::where('start', '>=', date("Y-m-d H:i:s"))
                        ->orderBy('start')
                        ->first();
        
        $event->setUrl($this->property('eventPage'),$this->controller);

        $this->event = $this->page['event'] = $event;
    }
}
