<?php namespace Inerba\Events\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInerbaEvents extends Migration
{
    public function up()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->dropColumn('start');
            $table->dropColumn('end');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');
        });
    }
}
