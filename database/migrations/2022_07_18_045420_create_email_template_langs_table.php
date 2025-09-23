<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplateLangsTable extends Migration
{
    public function up()
    {
        Schema::create('email_template_langs', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id');
            $table->string('lang', 100);
            $table->string('subject');
            $table->text('content');
            $table->timestamps();
        
        });
    }

    public function down()
    {
        Schema::dropIfExists('email_template_langs');
    }
}
