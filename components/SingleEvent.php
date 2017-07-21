<?php namespace Inerba\Events\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Inerba\Events\Models\Event;
use Inerba\Events\Models\Participant;
use RainLab\Location\Models\Setting as LocationSettings;

class SingleEvent extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Scheda Evento',
            'description' => 'Mostra la scheda del singolo evento'
        ];
    }

    public function defineProperties()
    {
        return [
            'postsPerPage' => [
                'title'             => 'rainlab.blog::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
        ];
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $event = Event::where('slug', $this->param('slug'))->first();

        // Aggiunge i link ai post
        $posts = $event->posts->each(function($post) {
            $post->setUrl( $this->property('postPage'), $this->controller);
        });

        //dd($event->toArray());
        
        // Evento non trovato
        if(empty($event)) return \Response::make($this->controller->run('404'), 404);

        $apiKey = LocationSettings::get('google_maps_key');
        $this->addJs('//maps.googleapis.com/maps/api/js?libraries=places&key='.$apiKey);
        $this->addJs('assets/js/jquery.gmap.js');

        $this->event = $this->page['event'] = $event;

        //dd($event->toArray());
    }

    public function onParticipate()
    {

        if(!empty(input('botcheck')) || empty(input('email'))){
            $this->page['errormsg'] = 'Si sono verificati degli errori, riprova o contattaci!';
            return false;
        }

        $event = Event::where('slug', $this->param('slug'))->first();

        // Codice per salvare le email nel database
        $participant = Participant::firstOrNew(['email' => input('email'), 'event_id' => $event->id ]);
        $participant->event_id = $event->id;
        
        if(!empty(input('name'))){
            $participant->name = input('name');
        }

        $participant->save();

        if($participant->wasRecentlyCreated){
            $this->page['successmsg'] = 'Grazie per la disponibilità!';
        } else {
            $this->page['successmsg'] = 'Ti eri già iscritto in precedenza!';
        }

    }
}
