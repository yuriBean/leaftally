<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_template_langs', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('lang', 100);
            $table->longText('content');
            $table->longText('variables');
            $table->integer('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_template_langs');
    }
};
