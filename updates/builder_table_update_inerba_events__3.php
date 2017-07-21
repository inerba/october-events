<?php namespace Inerba\Events\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInerbaEvents3 extends Migration
{
    public function up()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->text('setup')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->dropColumn('setup');
        });
    }
}
