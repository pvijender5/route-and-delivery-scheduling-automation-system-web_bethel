<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create location types table
        Schema::create('location_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->json('required_fields')->nullable();
            $table->json('validation_rules')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });

        // Create customer locations table
        Schema::create('customer_locations', function (Blueprint $table) {
            $table->id();
            // Basic information
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_type_id')->constrained('location_types')->onDelete('restrict');
            $table->string('location_code', 20)->unique();
            $table->string('location_name');
            $table->boolean('is_primary')->default(false);
            $table->enum('status', ['active', 'inactive', 'temporary'])->default('active');
            
            // Address information
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city', 50);
            $table->string('state', 50);
            $table->string('postal_code', 20);
            $table->string('country', 2)->default('US');
            $table->point('coordinates')->nullable();
            $table->string('what3words', 50)->nullable();
            $table->polygon('delivery_zone')->nullable();
            
            // Contact information
            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->string('contact_email')->nullable();
            $table->string('alternative_contact_name')->nullable();
            $table->string('alternative_contact_phone', 20)->nullable();
            
            // Delivery specifications
            $table->json('delivery_windows')->nullable();
            $table->json('blackout_dates')->nullable();
            $table->boolean('requires_appointment')->default(false);
            $table->integer('minimum_notice_hours')->default(0);
            $table->text('delivery_instructions')->nullable();
            $table->text('access_instructions')->nullable();
            
            // Facility details
            $table->boolean('has_loading_dock')->default(false);
            $table->boolean('has_forklift')->default(false);
            $table->boolean('requires_liftgate')->default(false);
            $table->boolean('has_security_check')->default(false);
            $table->string('dock_height', 20)->nullable();
            $table->string('parking_instructions')->nullable();
            $table->json('facility_features')->nullable();
            
            // Operating hours
            $table->json('regular_hours')->nullable();
            $table->json('special_hours')->nullable();
            $table->json('holiday_schedule')->nullable();
            
            // Delivery constraints
            $table->decimal('max_vehicle_height', 5, 2)->nullable();
            $table->decimal('max_vehicle_weight', 8, 2)->nullable();
            $table->decimal('max_vehicle_length', 5, 2)->nullable();
            $table->boolean('allows_overnight_parking')->default(false);
            $table->json('vehicle_restrictions')->nullable();
            
            // Risk and compliance
            $table->boolean('requires_safety_equipment')->default(false);
            $table->json('required_safety_equipment')->nullable();
            $table->boolean('has_hazardous_materials')->default(false);
            $table->json('safety_protocols')->nullable();
            $table->date('safety_certification_expiry')->nullable();
            
            // Metrics
            $table->integer('successful_deliveries')->default(0);
            $table->integer('failed_deliveries')->default(0);
            $table->decimal('average_delivery_time', 5, 2)->default(0);
            $table->json('delivery_metrics')->nullable();
            
            // Timestamps and soft delete
            $table->timestamps();
            $table->softDeletes();
            
            // Audit columns
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('restrict');
            $table->timestamp('verified_at')->nullable();

            // Indexes
            $table->index('customer_id');
            $table->index('location_code');
            $table->index('status');
            $table->index(['customer_id', 'is_primary']);
            $table->index('postal_code');
            $table->spatialIndex('coordinates');
            $table->spatialIndex('delivery_zone');
            $table->index('created_at');
        });

        // Create location attachments table
        Schema::create('location_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('customer_locations')->onDelete('cascade');
            $table->string('type', 50);
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type', 50);
            $table->bigInteger('file_size');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('location_id');
            $table->index('type');
        });

        // Create location access logs
        Schema::create('location_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('customer_locations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            $table->point('check_in_coordinates')->nullable();
            $table->point('check_out_coordinates')->nullable();
            $table->text('notes')->nullable();
            $table->json('safety_checklist_completed')->nullable();
            
            $table->index('location_id');
            $table->index('user_id');
            $table->index('check_in_time');
            $table->spatialIndex('check_in_coordinates');
        });

        // Insert default location types
        DB::table('location_types')->insert([
            [
                'name' => 'Warehouse',
                'description' => 'Large storage facility with loading docks',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Retail Store',
                'description' => 'Customer-facing retail location',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Distribution Center',
                'description' => 'Major distribution hub',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Office',
                'description' => 'Business office location',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_access_logs');
        Schema::dropIfExists('location_attachments');
        Schema::dropIfExists('customer_locations');
        Schema::dropIfExists('location_types');
    }
};
