<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemThumbnailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_thumbnails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('big');
            $table->string('medium')->nullable();
            $table->string('small')->nullable();
            $table->string('is_thumbnail')->default(\App\Models\ItemThumbnail::NO)
            ->comment('1=YES,0=NO');

            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('item_thumbnails',function (Blueprint $table){
            $table->dropForeign(['item_id']);
        });
        Schema::dropIfExists('item_thumbnails');
    }
}
