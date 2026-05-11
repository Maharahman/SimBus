<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('tickets', function (Blueprint $table) {
        // We add this to store the name of the Dev (you) who took the ticket
        $table->string('handled_by')->nullable()->after('status');
    });
}

public function down()
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('handled_by');
    });
}
};
