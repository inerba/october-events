<?php namespace Inerba\Events\Components;

use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Inerba\Events\Models\Event;
use Inerba\Events\Models\Participant;
use RainLab\Location\Models\Setting as LocationSettings;

class EventsList extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Lista eventi',
            'description' => 'Mostra la lista generale degli eventi'
        ];
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'rainlab.blog::lang.settings.posts_pagination',
                'description' => 'rainlab.blog::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'postsPerPage' => [
                'title'             => 'rainlab.blog::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'        => 'rainlab.blog::lang.settings.posts_no_posts',
                'description'  => 'rainlab.blog::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ],
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
            'maps_api_key' => [
                'title'       => 'Google static maps api key',
                'description' => 'static maps api key',
                'type'        => 'string',
                'default'     => '',
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
        $this->maps_api_key = $this->page['maps_api_key'] = $this->property('maps_api_key');
        $this->events = $this->page['events'] = $this->listEvents();
        $this->expired_events = $this->page['expired_events'] = $this->listExpiredEvents();
    }

    protected function listEvents()
    {
        $pages = $this->property('postsPerPage');
        $page = $this->param('page');

        $events = Event::isNotExpired()->orderBy('start','asc')->paginate($pages, $page);

        /*
         * Add a "url" helper attribute for linking to each event
         */
        $events->each(function($event) {
            $event->setUrl($this->property('eventPage'), $this->controller);
        });

        return $events;
    }

    protected function listExpiredEvents()
    {
        $pages = $this->property('postsPerPage');
        $page = $this->param('page');

        $events = Event::isExpired()
                    ->orderBy('start','desc')
                    ->paginate($pages, $page);

        $events->each(function($event) {
            $event->setUrl($this->property('eventPage'), $this->controller);
        });

        return $events;
    }
}
