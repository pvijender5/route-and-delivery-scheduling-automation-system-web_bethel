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
        // Create warehouse positions table
        Schema::create('warehouse_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('description')->nullable();
            $table->json('required_skills')->nullable();
            $table->json('responsibilities')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });

        // Create warehouse teams table
        Schema::create('warehouse_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained()->onDelete('cascade');
            $table->string('team_name');
            $table->string('team_code', 20)->unique();
            $table->enum('shift', ['morning', 'afternoon', 'night', 'rotating'])->default('morning');
            $table->enum('status', ['active', 'inactive', 'training'])->default('active');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->onDelete('restrict');
            $table->integer('min_members')->default(1);
            $table->integer('max_members')->default(10);
            $table->json('working_hours')->nullable();
            $table->json('break_schedule')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('warehouse_id');
            $table->index('team_code');
            $table->index('shift');
            $table->index('status');
            $table->index('supervisor_id');
        });

        // Create warehouse team members table
        Schema::create('warehouse_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('warehouse_teams')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->foreignId('position_id')->constrained('warehouse_positions')->onDelete('restrict');
            $table->date('joined_date');
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'training', 'on_leave', 'inactive'])->default('active');
            $table->json('certifications')->nullable();
            $table->json('skills')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['team_id', 'user_id']);
            $table->index('position_id');
            $table->index('status');
            $table->index('joined_date');
        });

        // Create warehouse team schedules table
        Schema::create('warehouse_team_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('warehouse_teams')->onDelete('cascade');
            $table->date('schedule_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('required_members')->default(1);
            $table->json('assigned_zones')->nullable();
            $table->json('tasks')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['team_id', 'schedule_date']);
            $table->index('schedule_date');
        });

        // Create warehouse team performance logs
        Schema::create('warehouse_team_performance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('warehouse_teams')->onDelete('cascade');
            $table->date('date');
            $table->integer('orders_processed')->default(0);
            $table->integer('items_picked')->default(0);
            $table->integer('items_packed')->default(0);
            $table->decimal('accuracy_rate', 5, 2)->default(0);
            $table->decimal('efficiency_rate', 5, 2)->default(0);
            $table->integer('errors_reported')->default(0);
            $table->json('performance_metrics')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['team_id', 'date']);
            $table->index('date');
        });

        // Insert default warehouse positions
        DB::table('warehouse_positions')->insert([
            [
                'name' => 'Team Lead',
                'description' => 'Supervises team operations and coordinates with management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Picker',
                'description' => 'Responsible for order picking and inventory management',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Packer',
                'description' => 'Handles order packing and quality control',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Forklift Operator',
                'description' => 'Operates forklift for material handling',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Quality Inspector',
                'description' => 'Ensures order accuracy and product quality',
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
        Schema::dropIfExists('warehouse_team_performance');
        Schema::dropIfExists('warehouse_team_schedules');
        Schema::dropIfExists('warehouse_team_members');
        Schema::dropIfExists('warehouse_teams');
        Schema::dropIfExists('warehouse_positions');
    }
};
