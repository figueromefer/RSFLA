<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\PropertyLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_documents_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get('/documents')
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_documents_module(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->get(route('documents.index'))->assertOk();
        $this->actingAs($staff)->get(route('documents.index'))->assertOk();
    }

    public function test_creating_document_link_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $property = $this->property();

        $this->actingAs($admin)
            ->post(route('documents.store'), [
                'property_id' => $property->id,
                'label' => 'Digital Brochure',
                'url' => 'https://example.com/brochure.pdf',
                'visible_to_client' => '1',
            ])
            ->assertRedirect(route('documents.index', ['property_id' => $property->id]));

        $this->assertDatabaseHas('property_links', [
            'property_id' => $property->id,
            'label' => 'Digital Brochure',
            'url' => 'https://example.com/brochure.pdf',
            'is_visible_to_client' => true,
        ]);
    }

    public function test_editing_document_link_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $propertyLink = PropertyLink::create([
            'property_id' => $this->property()->id,
            'label' => 'Old Link',
            'url' => 'https://example.com/old',
            'is_visible_to_client' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('documents.update', $propertyLink), [
                'property_id' => $propertyLink->property_id,
                'label' => 'Updated Link',
                'url' => 'https://example.com/updated',
                'visible_to_client' => '0',
            ])
            ->assertRedirect(route('documents.edit', $propertyLink));

        $this->assertDatabaseHas('property_links', [
            'id' => $propertyLink->id,
            'label' => 'Updated Link',
            'url' => 'https://example.com/updated',
            'is_visible_to_client' => false,
        ]);
    }

    public function test_deleting_document_link_works(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $propertyLink = PropertyLink::create([
            'property_id' => $this->property()->id,
            'label' => 'Delete Me',
            'url' => 'https://example.com/delete',
            'is_visible_to_client' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('documents.destroy', $propertyLink))
            ->assertRedirect(route('documents.index', ['property_id' => $propertyLink->property_id]));

        $this->assertDatabaseMissing('property_links', [
            'id' => $propertyLink->id,
        ]);
    }

    public function test_client_only_sees_visible_links(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        PropertyLink::create([
            'property_id' => $property->id,
            'label' => 'Visible Brochure',
            'url' => 'https://example.com/visible',
            'is_visible_to_client' => true,
        ]);
        PropertyLink::create([
            'property_id' => $property->id,
            'label' => 'Internal Dropbox',
            'url' => 'https://example.com/internal',
            'is_visible_to_client' => false,
        ]);

        $response = $this->actingAs($client)
            ->get(route('client.properties.show', $property));

        $response->assertOk();
        $response->assertSee('Visible Brochure');
        $response->assertDontSee('Internal Dropbox');
        $response->assertDontSee('https://example.com/internal');
    }

    private function property(): Property
    {
        return Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'is_active' => true,
        ]);
    }
}
