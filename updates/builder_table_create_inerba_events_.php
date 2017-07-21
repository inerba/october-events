<?php namespace Inerba\Events\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateInerbaEvents extends Migration
{
    public function up()
    {
        Schema::create('inerba_events_', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->text('address')->nullable();
            $table->text('attachments')->nullable();
            $table->text('media')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('inerba_events_');
    }
}
