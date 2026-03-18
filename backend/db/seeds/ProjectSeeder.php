<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class ProjectSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $data = [
            // Company 1 (Acme Inc)
            [
        'company_id' => 1,
        'name' => 'Website Redesign',
        'alias' => 'website-redesign',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 1,
        'name' => 'Mobile App 📱',
        'alias' => null,
        'is_deleted' => 0,
            ],

            // Company 2 (TechCorp)
            [
        'company_id' => 2,
        'name' => 'Cloud Migration ☁️',
        'alias' => 'cloud-migration',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 2,
        'name' => 'AI Integration',
        'alias' => '🤖-ai',
        'is_deleted' => 1,
            ],

            // Company 3 (StartupXYZ)
            [
        'company_id' => 3,
        'name' => 'MVP Launch 🚀',
        'alias' => null,
        'is_deleted' => 0,
            ],
            [
        'company_id' => 3,
        'name' => 'Beta Testing',
        'alias' => 'beta-testing',
        'is_deleted' => 0,
            ],

            // Company 4 (Blue Ocean Labs)
            [
        'company_id' => 4,
        'name' => 'Research & Development',
        'alias' => 'r-and-d',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 4,
        'name' => 'Data Analysis 📊',
        'alias' => null,
        'is_deleted' => 1,
            ],

            // Company 5 (Sunrise Studio)
            [
        'company_id' => 5,
        'name' => 'Creative Campaign ✨',
        'alias' => 'creative-campaign',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 5,
        'name' => 'Brand Guidelines',
        'alias' => null,
        'is_deleted' => 0,
            ],

            // Company 6 (Nimbus Cloud Co)
            [
        'company_id' => 6,
        'name' => 'Infrastructure Setup',
        'alias' => 'infra-setup',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 6,
        'name' => 'Monitoring & Alerts 🔔',
        'alias' => '🔔-monitoring',
        'is_deleted' => 0,
            ],

            // Company 7 (PixelForge) - This company is deleted
            [
        'company_id' => 7,
        'name' => 'Graphics Suite 🎨',
        'alias' => 'graphics-suite',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 7,
        'name' => 'Animation Framework',
        'alias' => null,
        'is_deleted' => 1,
            ],

            // Company 8 (GreenLeaf Consulting)
            [
        'company_id' => 8,
        'name' => 'Strategy Review 📋',
        'alias' => null,
        'is_deleted' => 0,
            ],
            [
        'company_id' => 8,
        'name' => 'Client Onboarding',
        'alias' => '🌿-onboarding',
        'is_deleted' => 0,
            ],

            // Company 9 (Midnight Ops) - This company is deleted
            [
        'company_id' => 9,
        'name' => 'Night Shift Operations 🌙',
        'alias' => 'night-ops',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 9,
        'name' => 'Security Audit',
        'alias' => null,
        'is_deleted' => 1,
            ],

            // Company 10 (Honeybee Works)
            [
        'company_id' => 10,
        'name' => 'Production Line',
        'alias' => 'production',
        'is_deleted' => 0,
            ],
            [
        'company_id' => 10,
        'name' => 'Quality Assurance 🐝',
        'alias' => null,
        'is_deleted' => 0,
            ],

            // Projects with null company_id (5 projects)
            [
        'company_id' => null,
        'name' => 'Internal Tooling',
        'alias' => 'internal-tools',
        'is_deleted' => 0,
            ],
            [
        'company_id' => null,
        'name' => 'Documentation Hub 📚',
        'alias' => null,
        'is_deleted' => 0,
            ],
            [
        'company_id' => null,
        'name' => 'Training Materials',
        'alias' => '📖-training',
        'is_deleted' => 1,
            ],
            [
        'company_id' => null,
        'name' => 'Open Source Contribution 🔓',
        'alias' => null,
        'is_deleted' => 0,
            ],
            [
        'company_id' => null,
        'name' => 'Community Outreach',
        'alias' => '🤝-community',
        'is_deleted' => 0,
            ],
        ];

        $table = $this->table('projects');
        $table->insert($data)->saveData();
    }
}
