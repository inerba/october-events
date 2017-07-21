<?php namespace Inerba\Events\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Participants extends Controller
{
    public $implement = ['Backend\Behaviors\ListController'];
    
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Inerba.Events', 'inerba-events', 'event-participants');
    }
}