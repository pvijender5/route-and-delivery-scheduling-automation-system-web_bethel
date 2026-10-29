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
        // Create shipment status history
        Schema::create('shipment_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->string('status', 20);
            $table->string('previous_status', 20)->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('status');
            $table->index('changed_by');
            $table->index('created_at');
        });

        // Create shipments table
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            // Basic information
            $table->string('tracking_number', 50)->unique();
            $table->enum('status', [
                'pending',
                'processing',
                'in_transit',
                'delivered',
                'failed',
                'cancelled',
                'returned'
            ])->default('pending');
            
            // Relationships
            $table->foreignId('customer_id')->constrained()->onDelete('restrict');
            $table->foreignId('route_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('warehouse_id')->constrained()->onDelete('restrict');
            $table->foreignId('driver_id')->nullable()->constrained('users')->onDelete('restrict');
            
            // Scheduling
            $table->date('scheduled_date');
            $table->time('delivery_window_start');
            $table->time('delivery_window_end');
            $table->timestamp('actual_delivery_time')->nullable();
            
            // Location information
            $table->foreignId('delivery_location_id')->constrained('customer_locations')->onDelete('restrict');
            $table->point('delivery_coordinates')->nullable();
            $table->string('delivery_zone', 50)->nullable();
            
            // Shipment details
            $table->decimal('total_weight', 10, 2)->default(0);
            $table->decimal('total_volume', 10, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->json('dimensions')->nullable();
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_temperature_controlled')->default(false);
            $table->decimal('temperature_min', 5, 2)->nullable();
            $table->decimal('temperature_max', 5, 2)->nullable();
            
            // Delivery instructions
            $table->text('delivery_instructions')->nullable();
            $table->text('special_handling_notes')->nullable();
            $table->json('customer_preferences')->nullable();
            
            // Contact information
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone', 20)->nullable();
            $table->string('recipient_email')->nullable();
            
            // Delivery attempt tracking
            $table->integer('delivery_attempts')->default(0);
            $table->json('delivery_attempt_history')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('proof_of_delivery_type')->nullable();
            $table->string('signature_image_path')->nullable();
            $table->string('photo_proof_path')->nullable();
            
            // Consolidation
            $table->foreignId('consolidation_id')->nullable()->constrained('shipment_consolidations')->onDelete('restrict');
            $table->integer('consolidation_sequence')->nullable();
            
            // Metrics and flags
            $table->boolean('is_priority')->default(false);
            $table->boolean('requires_attention')->default(false);
            $table->decimal('delivery_score', 5, 2)->nullable();
            $table->json('quality_metrics')->nullable();
            
            // Timestamps and soft delete
            $table->timestamps();
            $table->softDeletes();
            
            // Audit columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('restrict');

            // Indexes
            $table->index('tracking_number');
            $table->index('status');
            $table->index('scheduled_date');
            $table->index('customer_id');
            $table->index('route_id');
            $table->index('warehouse_id');
            $table->index('driver_id');
            $table->index('delivery_location_id');
            $table->index(['is_priority', 'status']);
            $table->index('created_at');
            $table->spatialIndex('delivery_coordinates');
        });

        // Create shipment items table
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('volume', 8, 2)->nullable();
            $table->json('dimensions')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();

            $table->index('shipment_id');
            $table->index('product_id');
        });

        // Create shipment temperature logs
        Schema::create('shipment_temperature_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 5, 2);
            $table->decimal('humidity', 5, 2)->nullable();
            $table->point('location')->nullable();
            $table->timestamp('recorded_at');
            $table->boolean('is_alert')->default(false);
            $table->text('notes')->nullable();

            $table->index('shipment_id');
            $table->index('recorded_at');
            $table->index('is_alert');
            $table->spatialIndex('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_temperature_logs');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipment_status_history');
    }
};

