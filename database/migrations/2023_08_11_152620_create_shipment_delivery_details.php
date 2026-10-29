<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shipment_delivery_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id')->nullable();
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade')->index()->name('delivery_details_route_id');
            $table->unsignedBigInteger('shipment_id')->nullable();
            $table->foreign('shipment_id')->references('id')->on('shipments')->onDelete('cascade')->index()->name('shipment_delivery_details_shipment_id');
            $table->integer('route_no')->nullable();
            $table->enum('delivery_status', ['new','planned','in-progress', 'posted-to-loadboard', 'driver-assigned', 'in-transit-on-time','in-transit-delay', 'delivered','canceled','undelivered','completed','loaded','assigned-to-warehouse'])->default('new');
            $table->boolean('is_replacing')->default(0);
            $table->float('eta')->default(0);
            $table->float('load')->default(0);
            $table->float('duration')->default(0);//minutes
            $table->float('distance')->default(0);//meter
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->boolean('on_time_delivery')->default(0);
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('undelivered_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->json('note_by_driver')->nullable();
            $table->text('fhf_point_note')->nullable();
            $table->string('customer_rating')->nullable();
            $table->string('customer_feedback')->nullable();
            $table->integer('P_quantity')->default(0);
            $table->integer('R_quantity')->default(0);
            $table->integer('T_quantity')->default(0);
            $table->boolean('loading_complete')->default(0);
            $table->boolean('customer_message_sent')->default(0);
            $table->unsignedBigInteger('loading_user')->nullable();
            $table->foreign('loading_user')->references('id')->on('users')->onDelete('cascade')->index()->name('shipment_delivery_details_loading_user');
            $table->string('delivery_latitude')->nullable();
            $table->string('delivery_longitude')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_delivery_details');
    }
};
