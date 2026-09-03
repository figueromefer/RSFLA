<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_properties_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/properties')
            ->assertForbidden();
    }

    public function test_staff_and_admin_can_access_properties_module(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($staff)->get('/properties')->assertOk();
        $this->actingAs($admin)->get('/properties')->assertOk();
    }

    public function test_creating_property_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Campus Plaza',
                'slug' => 'campus-plaza',
                'address' => '100 Main St',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'hero_image' => 'https://example.com/campus.jpg',
                'report_title' => 'Campus Plaza Report',
                'is_active' => '1',
            ])
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'name' => 'Campus Plaza',
            'slug' => 'campus-plaza',
            'street_address' => '100 Main St',
            'is_active' => true,
        ]);
    }

    public function test_slug_is_generated_when_blank(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'South Valley Retail',
                'slug' => '',
                'address' => '200 State St',
                'city' => 'Sandy',
                'state' => 'UT',
                'is_active' => '1',
            ])
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', [
            'name' => 'South Valley Retail',
            'slug' => 'south-valley-retail',
        ]);
    }

    public function test_client_cannot_view_inactive_property_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Inactive Property',
            'slug' => 'inactive-property',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => false,
            'status' => Property::STATUS_INACTIVE,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertForbidden();
    }

    public function test_properties_index_shows_report_first_actions_without_metrics(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Hidden Tenant',
            'tenant' => 'Hidden Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('properties.index'));

        $response->assertOk();
        $response->assertSee('Utah Campus');
        $response->assertSee('View Report');
        $response->assertSee('Edit Property');
        $response->assertSee('Leasing Activity');
        $response->assertDontSee('Total Prospects');
        $response->assertDontSee('Visible Prospects');
    }

    public function test_admin_and_staff_can_view_property_detail(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);

        $this->actingAs($staff)
            ->get(route('properties.show', $property))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('properties.show', $property))
            ->assertOk();
    }

    public function test_client_cannot_view_internal_property_detail(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('properties.show', $property))
            ->assertForbidden();
    }

    public function test_property_detail_shows_only_that_property_prospects_and_activity(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $otherProperty = Property::create([
            'name' => 'Other Property',
            'slug' => 'other-property',
            'city' => 'Provo',
            'state' => 'UT',
            'is_active' => true,
        ]);
        $prospect = Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        $otherProspect = Prospect::create([
            'property_id' => $otherProperty->id,
            'first_name' => 'Other Tenant',
            'tenant' => 'Other Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        ProspectActivity::create([
            'property_id' => $property->id,
            'prospect_id' => $prospect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Visible activity',
            'occurred_at' => now(),
        ]);
        ProspectActivity::create([
            'property_id' => $otherProperty->id,
            'prospect_id' => $otherProspect->id,
            'type' => ProspectActivity::TYPE_CREATED,
            'subject' => 'Other activity',
            'occurred_at' => now(),
        ]);

        $response = $this->actingAs($staff)
            ->get(route('properties.show', $property));

        $response->assertOk();
        $response->assertSee('Visible Tenant');
        $response->assertSee('Visible activity');
        $response->assertDontSee('Other Tenant');
        $response->assertDontSee('Other activity');
    }
    public function test_property_photo_can_be_uploaded(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Photo Property',
                'slug' => 'photo-property',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'is_active' => '1',
                'property_photo' => UploadedFile::fake()->image('property.jpg', 1200, 800),
            ])
            ->assertRedirect(route('properties.index'));

        $property = Property::where('slug', 'photo-property')->firstOrFail();
        $this->assertStringStartsWith('properties/', $property->hero_image);
        $this->assertSame(Storage::disk('public')->url($property->hero_image), $property->property_photo_url);
        Storage::disk('public')->assertExists($property->hero_image);
    }

    public function test_editing_property_replaces_previous_local_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('properties/old-photo.jpg', 'old photo');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $property = Property::create([
            'name' => 'Replace Photo Property',
            'slug' => 'replace-photo-property',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'hero_image' => 'properties/old-photo.jpg',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('properties.update', $property), [
                'name' => $property->name,
                'slug' => $property->slug,
                'city' => $property->city,
                'state' => $property->state,
                'is_active' => '1',
                'property_photo' => UploadedFile::fake()->image('replacement.webp', 1200, 800),
            ])
            ->assertRedirect(route('properties.edit', $property));

        $property->refresh();
        $this->assertNotSame('properties/old-photo.jpg', $property->hero_image);
        Storage::disk('public')->assertMissing('properties/old-photo.jpg');
        Storage::disk('public')->assertExists($property->hero_image);
    }

    public function test_existing_external_hero_image_still_renders(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = Property::create([
            'name' => 'External Photo Property',
            'slug' => 'external-photo-property',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'hero_image' => 'https://example.com/external-photo.jpg',
            'is_active' => true,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('https://example.com/external-photo.jpg', false);
    }

    public function test_invalid_property_photo_is_rejected(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Invalid Photo Property',
                'slug' => 'invalid-photo-property',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'is_active' => '1',
                'property_photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('property_photo');

        $this->assertDatabaseMissing('properties', ['slug' => 'invalid-photo-property']);
    }

    public function test_admin_and_staff_can_save_property_information(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $user = User::factory()->create(['role' => $role]);
            $property = Property::create([
                'name' => ucfirst($role).' Property',
                'slug' => $role.'-property-information',
                'city' => 'Salt Lake City',
                'state' => 'UT',
                'is_active' => true,
            ]);

            $this->actingAs($user)
                ->put(route('properties.update', $property), [
                    'name' => $property->name,
                    'slug' => $property->slug,
                    'city' => $property->city,
                    'state' => $property->state,
                    'property_information' => "Flexible details for {$role}.\nSecond line.",
                    'is_active' => '1',
                ])
                ->assertRedirect(route('properties.edit', $property));

            $this->assertDatabaseHas('properties', [
                'id' => $property->id,
                'property_information' => "Flexible details for {$role}.\nSecond line.",
            ]);
        }
    }

    public function test_property_edit_has_report_sections_and_hides_legacy_fields(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $property = Property::create([
            'name' => 'Utah Campus', 'slug' => 'utah-campus', 'city' => 'Salt Lake City',
            'state' => 'UT', 'report_title' => 'Legacy Report Title', 'is_active' => true,
        ]);

        $this->actingAs($staff)->get(route('properties.edit', $property))
            ->assertOk()
            ->assertSeeInOrder(['Property', 'Property Information', 'Market Data', 'Rent Roll', 'Status'])
            ->assertSee('General property information shown in the client report.')
            ->assertSee('View Report')
            ->assertDontSee('Assigned Team')
            ->assertDontSee('name="report_title"', false)
            ->assertDontSee('name="slug"', false);
    }

    public function test_admin_navigation_only_shows_report_workflow_modules(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('properties.index'))
            ->assertOk()
            ->assertSeeInOrder(['Properties', 'Leasing Activity', 'Marketing Activity', 'Documents', 'Users'])
            ->assertDontSee(route('dashboard'), false)
            ->assertDontSee(route('reports.index'), false)
            ->assertDontSee(route('team.index'), false)
            ->assertDontSee('Settings');
    }

    public function test_client_cannot_edit_property_information(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = Property::create([
            'name' => 'Protected Property',
            'slug' => 'protected-property-information',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'property_information' => 'Original information',
            'is_active' => true,
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->put(route('properties.update', $property), [
                'name' => $property->name,
                'slug' => $property->slug,
                'city' => $property->city,
                'state' => $property->state,
                'property_information' => 'Unauthorized change',
                'is_active' => '1',
            ])
            ->assertForbidden();

        $this->assertSame('Original information', $property->fresh()->property_information);
    }
}
