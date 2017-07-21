<?php namespace Inerba\Events\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInerbaEventsParticipants extends Migration
{
    public function up()
    {
        Schema::create('inerba_events_participants', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('email');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('inerba_events_participants');
    }
}
