<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{

Schema::create('saved_codes', function (Blueprint $table) {

$table->id();

$table->foreignId('user_id')
->constrained()
->cascadeOnDelete();

$table->text('code');

$table->string('language');

$table->timestamps();

});

}
};
