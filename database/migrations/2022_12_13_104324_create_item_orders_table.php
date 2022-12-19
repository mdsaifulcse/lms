<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no',20);
            $table->tinyInteger('qty',false,4)->default(0);
            $table->float('amount')->default(0);
            $table->timestamp('tentative_date')->comment('Tentative delivery date');

            $table->foreignId('vendor_id')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');

            $table->tinyInteger('status')->default(\App\Models\ItemOrder::ACTIVE)
                ->comment('1=Active,0=Inactive');
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
        Schema::table('item_orders',function (Blueprint $table){
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });
        Schema::dropIfExists('item_orders');
    }
}
