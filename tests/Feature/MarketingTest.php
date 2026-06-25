<?php

namespace Tests\Feature;

use App\Models\MarketingActivity;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_marketing_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/marketing')
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_marketing_module(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->get(route('marketing.index'))->assertOk();
        $this->actingAs($staff)->get(route('marketing.index'))->assertOk();
    }

    public function test_creating_marketing_activity_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = $this->property();

        $this->actingAs($admin)
            ->post(route('marketing.store'), [
                'property_id' => $property->id,
                'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
                'title' => 'Broadcast sent',
                'description' => 'Sent to broker list.',
                'activity_date' => '2026-06-25',
                'metric_label' => 'Recipients',
                'metric_value' => '500',
                'url' => 'https://example.com/email',
                'visible_to_client' => '1',
            ])
            ->assertRedirect(route('marketing.index', ['property_id' => $property->id]));

        $this->assertDatabaseHas('marketing_activities', [
            'property_id' => $property->id,
            'title' => 'Broadcast sent',
            'visible_to_client' => true,
        ]);
    }

    public function test_editing_marketing_activity_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $activity = $this->marketingActivity();

        $this->actingAs($admin)
            ->put(route('marketing.update', $activity), [
                'property_id' => $activity->property_id,
                'type' => MarketingActivity::TYPE_CAMPAIGN,
                'title' => 'Updated campaign',
                'description' => 'Updated description.',
                'activity_date' => '2026-06-26',
                'metric_label' => 'Impressions',
                'metric_value' => '1,200',
                'url' => 'https://example.com/campaign',
                'visible_to_client' => '0',
            ])
            ->assertRedirect(route('marketing.edit', $activity));

        $this->assertDatabaseHas('marketing_activities', [
            'id' => $activity->id,
            'title' => 'Updated campaign',
            'visible_to_client' => false,
        ]);
    }

    public function test_deleting_marketing_activity_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $activity = $this->marketingActivity();

        $this->actingAs($admin)
            ->delete(route('marketing.destroy', $activity))
            ->assertRedirect(route('marketing.index', ['property_id' => $activity->property_id]));

        $this->assertDatabaseMissing('marketing_activities', [
            'id' => $activity->id,
        ]);
    }

    public function test_client_only_sees_visible_marketing_activities(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->marketingActivity([
            'property_id' => $property->id,
            'title' => 'Visible campaign',
            'visible_to_client' => true,
        ]);
        $this->marketingActivity([
            'property_id' => $property->id,
            'title' => 'Internal campaign',
            'visible_to_client' => false,
        ]);

        $response = $this->actingAs($client)
            ->get(route('client.properties.show', $property));

        $response->assertOk();
        $response->assertSee('Visible campaign');
        $response->assertDontSee('Internal campaign');
    }

    public function test_property_detail_shows_marketing_for_that_property_only(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();
        $otherProperty = $this->property([
            'name' => 'Other Property',
            'slug' => 'other-property',
        ]);

        $this->marketingActivity([
            'property_id' => $property->id,
            'title' => 'Utah campaign',
        ]);
        $this->marketingActivity([
            'property_id' => $otherProperty->id,
            'title' => 'Other campaign',
        ]);

        $response = $this->actingAs($staff)
            ->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertSee('Utah campaign');
        $response->assertDontSee('Other campaign');
    }

    private function property(array $overrides = []): Property
    {
        return Property::create(array_merge([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ], $overrides));
    }

    private function marketingActivity(array $overrides = []): MarketingActivity
    {
        $propertyId = $overrides['property_id'] ?? $this->property()->id;

        return MarketingActivity::create(array_merge([
            'property_id' => $propertyId,
            'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
            'title' => 'Broadcast sent',
            'description' => 'Sent to broker list.',
            'activity_date' => '2026-06-25',
            'visible_to_client' => true,
        ], $overrides));
    }
}
