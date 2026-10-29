<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Shipment;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase; // This trait resets the database after each test

    public function testSoftDelete()
    {
        // Create a new shipment
        $shipment = Shipment::factory()->create();

        // Delete the shipment using soft delete
        $shipment->delete();

        // Check that the shipment has been marked as deleted
        $this->assertSoftDeleted('shipments', ['id' => $shipment->id]);

        // Restore the shipment (optional, if applicable)
        $shipment->restore();

        // Check that the shipment has been restored
        $this->assertDatabaseHas('shipments', ['id' => $shipment->id]);
    }
}

