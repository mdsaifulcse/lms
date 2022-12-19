<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->nullable();
            $table->text('description')->nullable();
            $table->string('icon_photo')->nullable();
            $table->tinyInteger('sequence')->default(0);
            $table->tinyInteger('status')->default(\App\Models\Category::ACTIVE)
                ->comment('1=Active,0=Inactive');
            $table->tinyInteger('show_home')->default(\App\Models\Category::NO)
                ->comment('1=Yes,0=No');

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
        Schema::dropIfExists('categories');
    }
}
