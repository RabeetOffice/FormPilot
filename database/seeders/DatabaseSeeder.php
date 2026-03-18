<?php

namespace Database\Seeders;

use App\Models\AiClassification;
use App\Models\Assignment;
use App\Models\Brand;
use App\Models\Domain;
use App\Models\FormSource;
use App\Models\Notification;
use App\Models\RoutingRule;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create workspace
        $workspace = Workspace::create([
            'name' => 'Acme Agency',
            'slug' => 'acme-agency',
            'description' => 'Full-service digital agency',
            'settings' => [
                'app_name' => 'Acme Agency',
                'primary_color' => '#4F46E5',
                'email_sender_name' => 'Acme Agency',
            ],
        ]);

        // 2. Create users
        $owner = User::create([
            'name' => 'Alex Owner',
            'email' => 'owner@acme.test',
            'password' => bcrypt('password'),
            'current_workspace_id' => $workspace->id,
        ]);

        $admin = User::create([
            'name' => 'Sarah Admin',
            'email' => 'admin@acme.test',
            'password' => bcrypt('password'),
            'current_workspace_id' => $workspace->id,
        ]);

        $salesRep = User::create([
            'name' => 'Mike Sales',
            'email' => 'sales@acme.test',
            'password' => bcrypt('password'),
            'current_workspace_id' => $workspace->id,
        ]);

        // Attach users to workspace
        $workspace->users()->attach($owner->id, ['role' => 'owner', 'accepted_at' => now()]);
        $workspace->users()->attach($admin->id, ['role' => 'admin', 'accepted_at' => now()]);
        $workspace->users()->attach($salesRep->id, ['role' => 'sales_rep', 'accepted_at' => now()]);

        // 3. Create brands
        $brandA = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'TechStart',
            'slug' => 'techstart',
            'color' => '#3B82F6',
            'description' => 'SaaS startup website',
        ]);

        $brandB = Brand::create([
            'workspace_id' => $workspace->id,
            'name' => 'GreenLeaf Co',
            'slug' => 'greenleaf-co',
            'color' => '#10B981',
            'description' => 'Eco-friendly products brand',
        ]);

        // 4. Create domains
        $domainA1 = Domain::create([
            'brand_id' => $brandA->id,
            'domain' => 'techstart.io',
            'api_key' => 'tk_' . Str::random(60),
            'allowed_origins' => ['https://techstart.io', 'http://localhost'],
        ]);

        $domainA2 = Domain::create([
            'brand_id' => $brandA->id,
            'domain' => 'app.techstart.io',
            'api_key' => 'tk_' . Str::random(60),
            'allowed_origins' => ['https://app.techstart.io'],
        ]);

        $domainB1 = Domain::create([
            'brand_id' => $brandB->id,
            'domain' => 'greenleaf.co',
            'api_key' => 'gl_' . Str::random(60),
            'allowed_origins' => ['https://greenleaf.co', 'http://localhost'],
        ]);

        $domainB2 = Domain::create([
            'brand_id' => $brandB->id,
            'domain' => 'shop.greenleaf.co',
            'api_key' => 'gl_' . Str::random(60),
            'allowed_origins' => ['https://shop.greenleaf.co'],
        ]);

        // 5. Form sources
        foreach ([$domainA1, $domainA2, $domainB1, $domainB2] as $domain) {
            FormSource::create(['domain_id' => $domain->id, 'name' => 'Default', 'type' => 'direct_post']);
            FormSource::create(['domain_id' => $domain->id, 'name' => 'JS Snippet', 'type' => 'js_snippet']);
        }

        // 6. Sample submissions
        $samples = [
            ['full_name' => 'John Carter', 'email' => 'john@bigcorp.com', 'phone' => '+14155551234', 'company' => 'BigCorp Inc', 'subject' => 'New Website', 'message' => 'We need a complete website redesign for our company. Budget is around $15,000. This is urgent as we want to launch before Q2.', 'budget' => '$15,000', 'domain' => $domainA1, 'brand' => $brandA, 'temp' => 'hot', 'service' => 'web_design', 'urgency' => 'high'],
            ['full_name' => 'Emily Zhang', 'email' => 'emily@startup.io', 'phone' => '+14155552345', 'company' => 'Startup Inc', 'subject' => 'SEO Help', 'message' => 'Looking for SEO services to improve our Google ranking. We are a SaaS company with about 50 pages.', 'budget' => '$3,000', 'domain' => $domainA1, 'brand' => $brandA, 'temp' => 'warm', 'service' => 'seo', 'urgency' => 'medium'],
            ['full_name' => 'Marcus Johnson', 'email' => 'marcus@gmail.com', 'phone' => '', 'company' => '', 'subject' => 'General Question', 'message' => 'Hi, I was wondering about your pricing for social media marketing.', 'budget' => '', 'domain' => $domainA2, 'brand' => $brandA, 'temp' => 'cold', 'service' => 'marketing', 'urgency' => 'low'],
            ['full_name' => 'Sofia Rodriguez', 'email' => 'sofia@greenco.com', 'phone' => '+14155553456', 'company' => 'Green Co', 'subject' => 'Branding Project', 'message' => 'We are rebranding our company and need a new visual identity, logo, and brand guidelines. Budget is flexible around $8,000.', 'budget' => '$8,000', 'domain' => $domainB1, 'brand' => $brandB, 'temp' => 'hot', 'service' => 'branding', 'urgency' => 'medium'],
            ['full_name' => 'David Park', 'email' => 'david@techventures.com', 'phone' => '+14155554567', 'company' => 'Tech Ventures', 'subject' => 'E-commerce Store', 'message' => 'I need to build an e-commerce store using WooCommerce. Looking for someone who can handle both design and development.', 'budget' => '$10,000', 'domain' => $domainB2, 'brand' => $brandB, 'temp' => 'hot', 'service' => 'ecommerce', 'urgency' => 'high'],
            ['full_name' => 'Lisa Chen', 'email' => 'lisa@email.com', 'phone' => '+14155555678', 'company' => 'Chen Design', 'subject' => 'Web Development', 'message' => 'Need a custom web application developed. Must have user authentication, dashboard, and API integration. Timeline: 3 months.', 'budget' => '$20,000', 'domain' => $domainA1, 'brand' => $brandA, 'temp' => 'hot', 'service' => 'development', 'urgency' => 'medium'],
            ['full_name' => 'James Wilson', 'email' => 'james.w@outlook.com', 'phone' => '', 'company' => 'Wilson & Co', 'subject' => 'Marketing Strategy', 'message' => 'We need consulting on our digital marketing strategy. Specifically interested in social media and PPC campaigns.', 'budget' => '$2,000', 'domain' => $domainA2, 'brand' => $brandA, 'temp' => 'warm', 'service' => 'consulting', 'urgency' => 'low'],
            ['full_name' => 'Anna Mueller', 'email' => 'anna@greenbusiness.de', 'phone' => '+491234567890', 'company' => 'Green Business GmbH', 'subject' => 'Sustainable Website', 'message' => 'Looking to create an eco-friendly website that showcases our sustainability initiatives. We appreciate great design.', 'budget' => '$5,000', 'domain' => $domainB1, 'brand' => $brandB, 'temp' => 'warm', 'service' => 'web_design', 'urgency' => 'medium'],
            ['full_name' => 'Test Spammer', 'email' => 'spam@mailinator.com', 'phone' => '', 'company' => '', 'subject' => 'BUY NOW', 'message' => 'Click here for free money! Buy now! Limited offer! Visit http://spam.com http://scam.com http://fake.com', 'budget' => '', 'domain' => $domainA1, 'brand' => $brandA, 'temp' => 'cold', 'service' => 'general_inquiry', 'urgency' => 'low', 'spam' => true],
            ['full_name' => 'Robert Taylor', 'email' => 'robert@taylordesign.com', 'phone' => '+14155556789', 'company' => 'Taylor Design', 'subject' => 'Website Audit', 'message' => 'Would like a professional audit of our current website. Interested in both UX and SEO improvements.', 'budget' => '$1,500', 'domain' => $domainB1, 'brand' => $brandB, 'temp' => 'warm', 'service' => 'consulting', 'urgency' => 'low'],
        ];

        // More submissions for volume
        for ($i = 0; $i < 15; $i++) {
            $samples[] = [
                'full_name' => fake()->name(),
                'email' => fake()->email(),
                'phone' => fake()->phoneNumber(),
                'company' => fake()->company(),
                'subject' => fake()->randomElement(['Website Inquiry', 'Quote Request', 'Partnership', 'General Question', 'Project Inquiry']),
                'message' => fake()->paragraph(3),
                'budget' => fake()->randomElement(['$500', '$1,000', '$2,500', '$5,000', '$10,000', '']),
                'domain' => fake()->randomElement([$domainA1, $domainA2, $domainB1, $domainB2]),
                'brand' => null, // will be set from domain
                'temp' => fake()->randomElement(['hot', 'warm', 'cold']),
                'service' => fake()->randomElement(['web_design', 'seo', 'marketing', 'branding', 'development', 'ecommerce', 'consulting', 'general_inquiry']),
                'urgency' => fake()->randomElement(['high', 'medium', 'low']),
            ];
        }

        foreach ($samples as $sample) {
            $isSpam = $sample['spam'] ?? false;
            $domain = $sample['domain'];
            $brand = $sample['brand'] ?? $domain->brand;

            $submission = Submission::create([
                'workspace_id' => $workspace->id,
                'brand_id' => $brand->id,
                'domain_id' => $domain->id,
                'form_source_id' => $domain->formSources()->first()?->id,
                'full_name' => $sample['full_name'],
                'first_name' => explode(' ', $sample['full_name'])[0] ?? '',
                'last_name' => explode(' ', $sample['full_name'])[1] ?? '',
                'email' => $sample['email'],
                'phone' => $sample['phone'],
                'company' => $sample['company'],
                'subject' => $sample['subject'],
                'message' => $sample['message'],
                'budget' => $sample['budget'],
                'page_url' => 'https://' . $domain->domain . '/contact',
                'referrer' => 'https://google.com',
                'utm_source' => fake()->randomElement(['google', 'facebook', 'linkedin', null]),
                'utm_medium' => fake()->randomElement(['cpc', 'organic', 'social', null]),
                'raw_payload' => ['name' => $sample['full_name'], 'email' => $sample['email']],
                'normalized_payload' => $sample,
                'ip_address' => fake()->ipv4(),
                'user_agent' => 'Mozilla/5.0',
                'status' => $isSpam ? 'archived' : fake()->randomElement(['new', 'open', 'in_progress', 'closed']),
                'spam_score' => $isSpam ? 8.5 : fake()->randomFloat(1, 0, 2),
                'is_spam' => $isSpam,
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);

            // AI Classification
            AiClassification::create([
                'submission_id' => $submission->id,
                'lead_temperature' => $sample['temp'],
                'service_type' => $sample['service'],
                'spam_probability' => $isSpam ? 0.95 : fake()->randomFloat(2, 0, 0.15),
                'urgency' => $sample['urgency'],
                'sentiment' => fake()->randomElement(['positive', 'neutral', 'negative']),
                'summary' => "{$sample['full_name']} inquired about {$sample['service']} via {$brand->name}.",
                'routing_recommendation' => "Route to {$sample['service']} team",
                'model_used' => 'rule_based_v1',
            ]);

            // Random assignments for non-spam
            if (!$isSpam && fake()->boolean(60)) {
                $assignee = fake()->randomElement([$owner, $admin, $salesRep]);
                Assignment::create([
                    'submission_id' => $submission->id,
                    'assigned_to' => $assignee->id,
                    'reason' => 'Auto-routing: ' . str_replace('_', ' ', $sample['service']),
                    'status' => 'active',
                    'assigned_at' => $submission->created_at,
                ]);
            }
        }

        // 7. Routing rules
        RoutingRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Web Design leads to Sarah',
            'type' => 'service_type',
            'conditions' => ['value' => 'web_design'],
            'target_user_id' => $admin->id,
            'priority' => 1,
        ]);

        RoutingRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'High-budget leads to Alex',
            'type' => 'budget',
            'conditions' => ['value' => '5000', 'operator' => 'gte'],
            'target_user_id' => $owner->id,
            'priority' => 0,
        ]);

        RoutingRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'GreenLeaf leads to Mike',
            'type' => 'brand',
            'conditions' => ['value' => (string)$brandB->id],
            'target_user_id' => $salesRep->id,
            'priority' => 2,
        ]);

        RoutingRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Fallback to Mike',
            'type' => 'fallback',
            'conditions' => [],
            'target_user_id' => $salesRep->id,
            'priority' => 99,
        ]);

        // 8. Sample notifications
        Notification::create([
            'workspace_id' => $workspace->id,
            'user_id' => $owner->id,
            'type' => 'new_submission',
            'title' => 'New Hot Lead from TechStart',
            'body' => 'John Carter submitted a form for a $15,000 website project.',
            'data' => ['brand_name' => 'TechStart'],
        ]);

        Notification::create([
            'workspace_id' => $workspace->id,
            'user_id' => $admin->id,
            'type' => 'assignment',
            'title' => 'New Lead Assigned',
            'body' => 'A web design lead has been assigned to you.',
            'data' => [],
        ]);

        echo "Seeded: 1 workspace, 3 users, 2 brands, 4 domains, 8 form sources, " . count($samples) . " submissions, routing rules, and notifications.\n";
    }
}
