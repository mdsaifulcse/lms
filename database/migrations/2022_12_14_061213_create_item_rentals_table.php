<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemRentalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_rentals', function (Blueprint $table) {
            $table->id();
            $table->string('rental_no',30);
            $table->timestamp('rental_date');
            $table->timestamp('return_date')->nullable();
            $table->tinyInteger('qty')->default(0);
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('status')->default(\App\Models\ItemRental::RENTAL)
                ->comment('0=Rental,1=Return,2=Overdue');

            $table->float('amount_of_penalty')->default(0);
            $table->string('note',255)->nullable();
            $table->tinyInteger('penalty_status')->default(\App\Models\ItemRental::NOAMOUNT)
                ->comment('0=NOAMOUNT,1=PAID,2=Due');
            $table->unsignedBigInteger('created_by', false);
            $table->unsignedBigInteger('updated_by', false)->nullable();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->cascadeOnDelete();

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
        Schema::table('item_rentals',function (Blueprint $table){
            $table->dropForeign(['user_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('item_rentals');
    }
}
