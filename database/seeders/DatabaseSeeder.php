<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\PropertyLink;
use App\Models\MarketingActivity;
use App\Models\Prospect;
use App\Models\ProspectActivity;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'RSFLA Admin',
            'email' => 'admin@rsfla.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        $staffUsers = collect([
            [
                'name' => 'Maya Reynolds',
                'email' => 'maya@rsfla.test',
                'title' => 'Leasing Director',
                'department' => 'Leasing',
            ],
            [
                'name' => 'Ethan Brooks',
                'email' => 'ethan@rsfla.test',
                'title' => 'Tour Coordinator',
                'department' => 'Operations',
            ],
            [
                'name' => 'Sofia Martinez',
                'email' => 'sofia@rsfla.test',
                'title' => 'Proposal Manager',
                'department' => 'Sales',
            ],
        ])->map(function (array $member) {
            $user = User::create([
                'name' => $member['name'],
                'email' => $member['email'],
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
            ]);

            return TeamMember::create([
                'user_id' => $user->id,
                'name' => $member['name'],
                'dre' => 'DRE '.fake()->unique()->numberBetween(1000000, 9999999),
                'email' => $member['email'],
                'phone' => fake()->phoneNumber(),
                'bio_url' => 'https://rsfla.com/team/'.str($member['name'])->slug(),
                'photo' => null,
                'title' => $member['title'],
                'department' => $member['department'],
            ]);
        });

        $client = User::create([
            'name' => 'Utah Campus Ownership',
            'email' => 'client@utahcampus.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        $clientSecondary = User::create([
            'name' => 'Jordan Park',
            'email' => 'owner@utahcampus.test',
            'password' => Hash::make('password'),
            'role' => User::ROLE_CLIENT,
        ]);

        $property = Property::create([
            'name' => 'Utah Campus',
            'slug' => 'utah-campus',
            'market' => 'Salt Lake City / University of Utah',
            'street_address' => '1150 E 500 S',
            'city' => 'Salt Lake City',
            'state' => 'UT',
            'hero_image' => 'https://images.unsplash.com/photo-1494526585095-c41746248156',
            'report_title' => 'Utah Campus Leasing Report',
            'postal_code' => '84102',
            'property_type' => 'student_housing',
            'unit_count' => 248,
            'owner_name' => 'Utah Campus Ownership Group',
            'status' => Property::STATUS_ACTIVE,
            'is_active' => true,
            'notes' => 'Student housing asset serving University of Utah demand.',
        ]);

        $property->clients()->attach([
            $client->id => ['role' => 'owner', 'receives_reports' => true],
            $clientSecondary->id => ['role' => 'asset_manager', 'receives_reports' => true],
        ]);
        $property->teamMembers()->sync($staffUsers->pluck('id')->all());

        $links = [
            ['Dropbox Leasing Folder', PropertyLink::TYPE_DROPBOX, 'https://www.dropbox.com/scl/fo/utah-campus-leasing', 'Shared leasing materials and current assets.'],
            ['Broadcast Email Archive', PropertyLink::TYPE_BROADCAST_EMAIL, 'https://mailchi.mp/rsfla/utah-campus-archive', 'Recent broadcast emails sent to prospect lists.'],
            ['Digital Brochure', PropertyLink::TYPE_BROCHURE, 'https://example.com/utah-campus-brochure.pdf', 'Current owner-approved brochure.'],
            ['Property Files', PropertyLink::TYPE_FILE, 'https://example.com/utah-campus-files', 'Client-visible reporting files and media.'],
        ];

        foreach ($links as $index => [$label, $type, $url, $description]) {
            PropertyLink::create([
                'property_id' => $property->id,
                'label' => $label,
                'type' => $type,
                'url' => $url,
                'description' => $description,
                'sort_order' => $index + 1,
            ]);
        }

        MarketingActivity::create([
            'property_id' => $property->id,
            'user_id' => $admin->id,
            'type' => MarketingActivity::TYPE_BROADCAST_EMAIL,
            'title' => 'June leasing broadcast sent',
            'description' => 'Owner-approved leasing update sent to broker and tenant prospect list.',
            'activity_date' => now()->subDays(3)->toDateString(),
            'metric_label' => 'Recipients',
            'metric_value' => '1,248',
            'url' => 'https://mailchi.mp/rsfla/utah-campus-june',
            'visible_to_client' => true,
        ]);
        MarketingActivity::create([
            'property_id' => $property->id,
            'user_id' => $admin->id,
            'type' => MarketingActivity::TYPE_BROKER_OUTREACH,
            'title' => 'Broker outreach follow-up',
            'description' => 'Internal follow-up list for broker calls and tenant rep conversations.',
            'activity_date' => now()->subDay()->toDateString(),
            'metric_label' => 'Calls',
            'metric_value' => '18',
            'visible_to_client' => false,
        ]);

        $prospectRows = [
            ['Avery', 'Kim', 'avery.kim@example.com', 'Resident referral', Prospect::STATUS_LEAD, 'Interested in 4x4 units for fall move-in.', 'Peak Fitness', 'Suite 110', 'Fitness', 'Q3 2026', 4600, 'Northline Realty', 'Avery Kim'],
            ['Logan', 'Hayes', 'logan.hayes@example.com', 'Website', Prospect::STATUS_TOUR_SCHEDULED, 'Tour scheduled with roommate group.', 'Campus Dental Group', 'Suite 205', 'Dental', 'Immediate', 2800, 'Wasatch CRE', 'Logan Hayes'],
            ['Priya', 'Shah', 'priya.shah@example.com', 'Instagram', Prospect::STATUS_TOUR_COMPLETED, 'Completed virtual tour and requested pricing.', 'U Coffee Lab', 'Suite 101', 'Cafe', 'Q4 2026', 1750, 'Direct', 'Priya Shah'],
            ['Marcus', 'Lee', 'marcus.lee@example.com', 'University housing fair', Prospect::STATUS_PROPOSAL_SENT, 'Proposal sent for premium floor plan.', 'Summit Tutoring', 'Suite 304', 'Education', 'Fall 2026', 3200, 'Bridge Commercial', 'Marcus Lee'],
            ['Emma', 'Walker', 'emma.walker@example.com', 'Google Ads', Prospect::STATUS_PROPOSAL_ACCEPTED, 'Accepted proposal pending lease package.', 'Trailhead Retail', 'Suite 120', 'Retail', 'Q3 2026', 5100, 'Intermountain Advisors', 'Emma Walker'],
            ['Noah', 'Patel', 'noah.patel@example.com', 'Parent referral', Prospect::STATUS_LEASE_SIGNED, 'Lease signed for August start.', 'Redstone Market', 'Suite 140', 'Market', 'Executed', 3900, 'Direct', 'Noah Patel'],
            ['Grace', 'Nguyen', 'grace.nguyen@example.com', 'Zillow', Prospect::STATUS_INACTIVE, 'Paused search until spring semester.', 'Beehive Wellness', 'Suite 215', 'Wellness', 'Paused', 2400, 'Salt Lake Retail Partners', 'Grace Nguyen'],
            ['Carlos', 'Rivera', 'carlos.rivera@example.com', 'Campus ambassador', Prospect::STATUS_PROSPECT, 'New inquiry for studio availability.', 'Crimson Apparel', 'Suite 118', 'Apparel', 'Exploring', 2100, 'Direct', 'Carlos Rivera'],
        ];

        foreach ($prospectRows as $index => [$firstName, $lastName, $email, $source, $status, $notes, $tenant, $suite, $useType, $timing, $rsf, $broker, $contactName]) {
            $teamMember = $staffUsers[$index % $staffUsers->count()];
            $lastContactedAt = now()->subDays($index + 1)->setTime(10 + ($index % 6), 15);

            $prospect = Prospect::create([
                'property_id' => $property->id,
                'assigned_team_member_id' => $teamMember->id,
                'suite' => $suite,
                'tenant' => $tenant,
                'use_type' => $useType,
                'timing' => $timing,
                'rsf' => $rsf,
                'broker' => $broker,
                'contact_name' => $contactName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => fake()->phoneNumber(),
                'source' => $source,
                'status' => $status,
                'budget' => fake()->numberBetween(1100, 2200),
                'desired_move_in' => now()->addMonths(fake()->numberBetween(1, 5))->startOfMonth(),
                'last_contacted_at' => $lastContactedAt,
                'is_active' => $status !== Prospect::STATUS_INACTIVE,
                'notes' => $notes,
                'visible_to_client' => $index !== 6,
                'sort_order' => $index + 1,
            ]);

            ProspectActivity::create([
                'prospect_id' => $prospect->id,
                'property_id' => $property->id,
                'user_id' => $teamMember->user_id,
                'team_member_id' => $teamMember->id,
                'type' => ProspectActivity::TYPE_STATUS_CHANGE,
                'status_from' => Prospect::STATUS_PROSPECT,
                'status_to' => $status,
                'subject' => 'Prospect moved to '.str_replace('_', ' ', $status),
                'body' => $notes,
                'occurred_at' => $lastContactedAt,
                'meta' => ['source' => $source],
            ]);

            ProspectActivity::create([
                'prospect_id' => $prospect->id,
                'property_id' => $property->id,
                'user_id' => $admin->id,
                'team_member_id' => $teamMember->id,
                'type' => ProspectActivity::TYPE_NOTE,
                'subject' => 'Owner report note added',
                'body' => 'Included in Utah Campus daily owner reporting snapshot.',
                'occurred_at' => $lastContactedAt->copy()->addHours(3),
            ]);
        }
    }
}
