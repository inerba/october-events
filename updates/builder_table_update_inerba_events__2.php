<?php namespace Inerba\Events\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableUpdateInerbaEvents2 extends Migration
{
    public function up()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->dropColumn('attachments');
        });
    }
    
    public function down()
    {
        Schema::table('inerba_events_', function($table)
        {
            $table->text('attachments')->nullable();
        });
    }
}
