<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyMarketDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_staff_can_create_market_data_entries(): void
    {
        Storage::fake('public');

        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(['slug' => "market-{$role}"]);
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->post(route('properties.market-data.store', $property), [
                'title' => "{$role} Market Stats",
                'report_date' => '2020-11-12',
                'image' => UploadedFile::fake()->image("{$role}.png", 1600, 1000),
            ])->assertRedirect(route('properties.edit', $property));

            $entry = $property->marketDataEntries()->where('title', "{$role} Market Stats")->firstOrFail();
            $this->assertStringStartsWith('market-data/', $entry->image_path);
            Storage::disk('public')->assertExists($entry->image_path);
        }
    }

    public function test_admin_and_staff_can_edit_market_data_without_replacing_image(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(['slug' => "edit-market-{$role}"]);
            $entry = $property->marketDataEntries()->create([
                'title' => 'Old Market Title',
                'image_path' => 'market-data/existing.png',
            ]);
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->put(route('properties.market-data.update', [$property, $entry]), [
                'title' => "Updated {$role} Market",
                'report_date' => '2021-01-15',
            ])->assertRedirect(route('properties.edit', $property));

            $entry->refresh();
            $this->assertSame("Updated {$role} Market", $entry->title);
            $this->assertSame('market-data/existing.png', $entry->image_path);
        }
    }

    public function test_admin_and_staff_can_replace_market_data_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('market-data/old.png', 'old image');
        $property = $this->property();
        $entry = $property->marketDataEntries()->create([
            'title' => 'Market Stats',
            'image_path' => 'market-data/old.png',
        ]);
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);

        $this->actingAs($staff)->put(route('properties.market-data.update', [$property, $entry]), [
            'title' => 'Market Stats Updated',
            'image' => UploadedFile::fake()->image('replacement.webp', 1800, 1200),
        ])->assertRedirect(route('properties.edit', $property));

        $entry->refresh();
        $this->assertNotSame('market-data/old.png', $entry->image_path);
        Storage::disk('public')->assertMissing('market-data/old.png');
        Storage::disk('public')->assertExists($entry->image_path);
    }

    public function test_admin_and_staff_can_delete_market_data_entry_and_local_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('market-data/delete.png', 'image');
        $property = $this->property();
        $entry = $property->marketDataEntries()->create([
            'title' => 'Delete Market Stats',
            'image_path' => 'market-data/delete.png',
        ]);
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->delete(route('properties.market-data.destroy', [$property, $entry]))
            ->assertRedirect(route('properties.edit', $property));

        $this->assertDatabaseMissing('property_market_data', ['id' => $entry->id]);
        Storage::disk('public')->assertMissing('market-data/delete.png');
    }

    public function test_client_cannot_access_market_data_administration(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)->get(route('properties.market-data.create', $property))->assertForbidden();
        $this->actingAs($client)->post(route('properties.market-data.store', $property), [
            'title' => 'Blocked Market Data',
        ])->assertForbidden();
        $this->assertDatabaseMissing('property_market_data', ['title' => 'Blocked Market Data']);
    }

    public function test_property_edit_shows_structured_market_data_fields_instead_of_legacy_image_entries(): void
    {
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $property = $this->property();
        $property->marketDataEntries()->create([
            'title' => 'Downtown Market Stats',
            'image_path' => 'market-data/downtown.png',
            'report_date' => '2020-11-12',
        ]);

        $this->actingAs($staff)->get(route('properties.edit', $property))
            ->assertOk()
            ->assertSee('name="market_rba"', false)
            ->assertSee('name="market_vacancy"', false)
            ->assertSee('name="market_sublet_percentage"', false)
            ->assertSee('name="market_ytd_absorption"', false)
            ->assertSee('name="market_notes"', false)
            ->assertSee('Market Data Subtitle')
            ->assertSee('Short subtitle displayed below Market Data in the client report.')
            ->assertSee('id="market_notes" name="market_notes" type="text"', false)
            ->assertDontSee('<textarea id="market_notes"', false)
            ->assertDontSee('Downtown Market Stats')
            ->assertDontSee('/storage/market-data/downtown.png', false);
    }

    public function test_report_shows_only_correct_property_market_data_and_us_date(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $otherProperty = $this->property(['name' => 'Other Property', 'slug' => 'other-market-property']);
        $property->clients()->attach($client->id);
        $property->marketDataEntries()->create([
            'title' => 'Target Market Stats',
            'image_path' => 'market-data/target.png',
            'report_date' => '2020-11-12',
        ]);
        $otherProperty->marketDataEntries()->create([
            'title' => 'Other Property Market Stats',
            'image_path' => 'market-data/other.png',
        ]);

        $this->actingAs($client)->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertDontSee('Target Market Stats')
            ->assertDontSee('11-12-2020')
            ->assertDontSee('/storage/market-data/target.png', false)
            ->assertDontSee('Other Property Market Stats')
            ->assertDontSee('/storage/market-data/other.png', false);
    }

    public function test_admin_and_staff_can_save_structured_market_data_and_report_displays_it(): void
    {
        foreach ([User::ROLE_ADMIN, User::ROLE_STAFF] as $role) {
            $property = $this->property(['slug' => "structured-market-{$role}"]);
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)->put(route('properties.update', $property), [
                'name' => $property->name,
                'slug' => $property->slug,
                'address' => $property->street_address,
                'city' => $property->city,
                'state' => $property->state,
                'is_active' => '1',
                'market_rba' => '$5.73',
                'market_vacancy' => '15.7%',
                'market_sublet_percentage' => '8.4%',
                'market_ytd_absorption' => '(285K)',
                'market_notes' => 'Short market commentary.',
            ])->assertRedirect(route('properties.edit', $property));

            $this->assertDatabaseHas('properties', ['id' => $property->id, 'market_rba' => '$5.73', 'market_notes' => 'Short market commentary.']);

            $this->actingAs($user)->get(route('properties.report', $property))
                ->assertOk()
                ->assertSeeInOrder(['Market Data', 'Short market commentary.', 'RBA'])
                ->assertSee('$5.73')->assertSee('15.7%')->assertSee('8.4%')->assertSee('(285K)')
                ->assertDontSee('market-notes', false)
                ->assertDontSee('>Notes<', false);
        }
    }

    public function test_market_data_image_is_required_and_validated_on_create(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $property = $this->property();

        $this->actingAs($admin)->post(route('properties.market-data.store', $property), [
            'title' => 'Missing Image',
        ])->assertSessionHasErrors('image');

        $this->actingAs($admin)->post(route('properties.market-data.store', $property), [
            'title' => 'Oversized Image',
            'image' => UploadedFile::fake()->image('oversized.png')->size(10241),
        ])->assertSessionHasErrors('image');

        $this->actingAs($admin)->post(route('properties.market-data.store', $property), [
            'title' => 'Invalid File',
            'image' => UploadedFile::fake()->create('market.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('image');
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
}
