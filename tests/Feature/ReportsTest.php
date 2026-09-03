<?php

namespace Tests\Feature;

use App\Models\MarketingActivity;
use App\Models\Property;
use App\Models\Prospect;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_access_reports_module(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);

        $this->actingAs($client)
            ->get(route('reports.index'))
            ->assertForbidden();
    }

    public function test_admin_and_staff_can_access_reports_module(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);

        $this->actingAs($admin)->get(route('reports.index'))->assertOk();
        $this->actingAs($staff)->get(route('reports.index'))->assertOk();
    }

    public function test_reports_index_shows_properties(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();
        Prospect::create([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ]);
        MarketingActivity::create([
            'property_id' => $property->id,
            'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
            'title' => 'Visible broadcast',
            'activity_date' => '2026-06-25',
            'visible_to_client' => true,
        ]);

        $response = $this->actingAs($staff)->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Utah Campus');
        $response->assertSee('Visible Prospects');
        $response->assertSee('Activity');
        $response->assertSee('View Report');
    }

    public function test_internal_report_show_responds_for_admin_and_staff(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();

        $this->actingAs($admin)
            ->get(route('reports.show', $property))
            ->assertOk()
            ->assertSee('Print / Export');

        $this->actingAs($staff)
            ->get(route('reports.show', $property))
            ->assertOk();
    }

    public function test_client_report_remains_protected_by_property_assignment(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $assignedProperty = $this->property();
        $unassignedProperty = $this->property([
            'name' => 'Unassigned Property',
            'slug' => 'unassigned-property',
        ]);
        $assignedProperty->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $assignedProperty))
            ->assertOk();

        $this->actingAs($client)
            ->get(route('client.properties.show', $unassignedProperty))
            ->assertForbidden();
    }

    public function test_print_button_appears_in_client_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Print / Export');
    }

    public function test_client_report_does_not_show_executive_summary(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertDontSee('Executive Summary')
            ->assertDontSee('Current leasing position');
    }

    public function test_client_report_groups_pipeline_by_status(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Lease Tenant',
            'status' => Prospect::STATUS_LEASE_SIGNED,
        ]);
        $this->prospect($property, [
            'tenant' => 'Proposal Tenant',
            'status' => Prospect::STATUS_PROPOSAL_SENT,
        ]);
        $this->prospect($property, [
            'tenant' => 'Tour Tenant',
            'status' => Prospect::STATUS_TOUR_SCHEDULED,
        ]);
        $this->prospect($property, [
            'tenant' => 'Active Tenant',
            'status' => Prospect::STATUS_PROSPECT,
        ]);
        $this->prospect($property, [
            'tenant' => 'Lead Tenant',
            'status' => Prospect::STATUS_LEAD,
        ]);
        $this->prospect($property, [
            'tenant' => 'Inactive Tenant',
            'status' => Prospect::STATUS_INACTIVE,
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Leasing Activity')
            ->assertSeeInOrder([
                'Current Active Prospects',
                'Active Tenant',
                'Leases',
                'Lease Tenant',
                'Proposals',
                'Proposal Tenant',
                'Tours',
                'Tour Tenant',
                'Inquiries',
                'Lead Tenant',
            ])
            ->assertDontSee('>Prospects<', false)
            ->assertDontSee('>Inquiry<', false)
            ->assertDontSee('Inactive Tenant');
    }

    public function test_client_report_shows_public_prospect_fields_and_hides_private_fields(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Visible Client Tenant',
            'opportunity_date' => '2026-07-15',
            'public_notes' => 'Public note for owner report.',
            'company' => 'Private Company LLC',
            'contact_name' => 'Private Contact Name',
            'email' => 'private-prospect@example.com',
            'phone' => '555-0188',
            'notes' => 'Private internal note only.',
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property, false).'?range=all')
            ->assertOk()
            ->assertSee('Visible Client Tenant')
            ->assertSee('07-15-2026')
            ->assertSee('Public Notes:')
            ->assertSee('Public note for owner report.')
            ->assertDontSee('Private Company LLC')
            ->assertDontSee('Private Contact Name')
            ->assertDontSee('private-prospect@example.com')
            ->assertDontSee('555-0188')
            ->assertDontSee('Private internal note only.');
    }

    public function test_client_report_kpis_link_to_report_sections(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('id="pipeline-prospects"', false)
            ->assertSee('id="pipeline-tours"', false)
            ->assertSee('id="pipeline-proposals"', false)
            ->assertSee('id="marketing-activity"', false);
    }

    public function test_client_report_pipeline_sections_are_collapsible(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $response = $this->actingAs($client)
            ->get(route('client.properties.show', $property));

        $response->assertOk()
            ->assertSee('<details id="pipeline-leases"', false)
            ->assertSee('<details id="pipeline-proposals"', false)
            ->assertSee('<details id="pipeline-tours"', false)
            ->assertSee('<details id="pipeline-prospects"', false)
            ->assertSee('<details id="pipeline-inquiry"', false)
            ->assertSee('<summary', false)
            ->assertSee('Active prospects currently being tracked')
            ->assertSee('No activity in this stage.');
    }

    public function test_internal_prospects_do_not_appear_on_client_report(): void
    {
        $client = User::factory()->create([
            'role' => User::ROLE_CLIENT,
        ]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Visible Tenant',
            'visible_to_client' => true,
        ]);
        $this->prospect($property, [
            'tenant' => 'Internal Tenant',
            'visible_to_client' => false,
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Visible Tenant')
            ->assertDontSee('Internal Tenant');
    }

    public function test_internal_reports_route_uses_polished_report_layout(): void
    {
        $staff = User::factory()->create([
            'role' => User::ROLE_STAFF,
        ]);
        $property = $this->property();

        $this->actingAs($staff)
            ->get(route('reports.show', $property))
            ->assertOk()
            ->assertDontSee('Executive Summary')
            ->assertSee('Leasing Activity')
            ->assertSee('Marketing Activity');
    }

    public function test_last_15_days_is_default_and_counts_match_detail_records(): void
    {
        $this->travelTo(Carbon::parse('2026-08-10 12:00:00'));
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->prospect($property, [
            'tenant' => 'Recent Tour',
            'status' => Prospect::STATUS_TOUR_SCHEDULED,
            'opportunity_date' => now()->subDays(5)->toDateString(),
        ]);
        $this->prospect($property, [
            'tenant' => 'Older Tour',
            'status' => Prospect::STATUS_TOUR_COMPLETED,
            'opportunity_date' => now()->subDays(20)->toDateString(),
        ]);

        $response = $this->actingAs($client)->get(route('client.properties.show', $property));

        $response->assertOk()
            ->assertSee('Last 15 Days')
            ->assertSee('All Time')
            ->assertSee('Recent Tour')
            ->assertDontSee('Older Tour');
        $this->assertSame('15', $response->viewData('range'));
        $this->assertSame(1, $response->viewData('metrics')['tours']);
        $this->assertSame(['Recent Tour'], $response->viewData('reportProspects')->pluck('tenant')->all());
    }

    public function test_all_time_includes_older_records_on_client_and_internal_reports(): void
    {
        $this->travelTo(Carbon::parse('2026-08-10 12:00:00'));
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $staff = User::factory()->create(['role' => User::ROLE_STAFF]);
        $property = $this->property();
        $property->clients()->attach($client->id);
        $this->prospect($property, [
            'tenant' => 'Older Inquiry',
            'opportunity_date' => now()->subDays(30)->toDateString(),
        ]);

        $clientResponse = $this->actingAs($client)->get(route('client.properties.show', $property, false).'?range=all');
        $clientResponse->assertOk()->assertSee('Older Inquiry');
        $this->assertSame('all', $clientResponse->viewData('range'));

        $this->actingAs($staff)
            ->get(route('reports.show', $property, false).'?range=all')
            ->assertOk()
            ->assertSee('Older Inquiry');
    }

    public function test_opportunity_date_takes_precedence_over_recent_created_at(): void
    {
        $this->travelTo(Carbon::parse('2026-08-10 12:00:00'));
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);
        $this->prospect($property, [
            'tenant' => 'Old Opportunity Recent Record',
            'opportunity_date' => now()->subDays(30)->toDateString(),
        ]);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertDontSee('Old Opportunity Recent Record');
    }

    public function test_created_at_is_fallback_when_opportunity_date_is_null(): void
    {
        $this->travelTo(Carbon::parse('2026-08-10 12:00:00'));
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);
        $recent = $this->prospect($property, ['tenant' => 'Recent Legacy Prospect', 'opportunity_date' => null]);
        $old = $this->prospect($property, ['tenant' => 'Old Legacy Prospect', 'opportunity_date' => null]);
        $old->forceFill(['created_at' => now()->subDays(30), 'updated_at' => now()->subDays(30)])->save();

        $response = $this->actingAs($client)->get(route('client.properties.show', $property));

        $response->assertOk()->assertSee($recent->tenant)->assertDontSee($old->tenant);
    }


    public function test_property_information_displays_with_line_breaks_and_report_sections_remain(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property([
            'property_information' => "First information line\nSecond information line",
        ]);
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Property Information')
            ->assertSee("First information line\nSecond information line")
            ->assertSee('whitespace-pre-line', false)
            ->assertSee('Marketing Activity')
            ->assertSee('Market Data')
            ->assertSee('Rent Roll')
            ->assertDontSee('Executive Summary')
            ->assertDontSee('Current leasing position');
    }

    public function test_empty_property_information_has_subtle_empty_state(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)
            ->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('No property information available.');
    }
    public function test_leasing_activity_uses_approved_subtitles(): void
    {
        $client = User::factory()->create(['role' => User::ROLE_CLIENT]);
        $property = $this->property();
        $property->clients()->attach($client->id);

        $this->actingAs($client)->get(route('client.properties.show', $property))
            ->assertOk()
            ->assertSee('Leases in negotiation')
            ->assertSee('Proposals in negotiations')
            ->assertSee('New opportunities')
            ->assertDontSee('Signed leases')
            ->assertDontSee('Proposals currently in progress')
            ->assertDontSee('New opportunities received');
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

    private function prospect(Property $property, array $overrides = []): Prospect
    {
        return Prospect::create(array_merge([
            'property_id' => $property->id,
            'first_name' => 'Visible Tenant',
            'tenant' => 'Visible Tenant',
            'suite' => '100',
            'use_type' => 'Retail',
            'timing' => 'Q3',
            'rsf' => 2500,
            'broker' => 'Broker Team',
            'status' => Prospect::STATUS_LEAD,
            'visible_to_client' => true,
            'is_active' => true,
        ], $overrides));
    }
}
