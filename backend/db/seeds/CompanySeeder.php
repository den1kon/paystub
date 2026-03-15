<?php

declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

class CompanySeeder extends AbstractSeed
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
                  [
                      'name' => 'Acme Inc',
                      'alias' => 'acme',
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'TechCorp 🤖',
                      'alias' => 'techcorp',
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'StartupXYZ',
                      // no alias on purpose
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'Blue Ocean Labs 🌊',
                      'alias' => 'blue-ocean',
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'Sunrise Studio',
                      'alias' => 'sunrise-☀️', // emoji in alias on purpose
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'Nimbus Cloud Co ☁️',
                      // no alias on purpose
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'PixelForge',
                      'alias' => 'pixelforge',
                      'is_deleted' => 1, // deleted
                  ],
                  [
                      'name' => 'GreenLeaf Consulting 🍃',
                      'alias' => 'greenleaf',
                      'is_deleted' => 0,
                  ],
                  [
                      'name' => 'Midnight Ops',
                      'alias' => 'midnight-ops 🌙', // emoji + space in alias on purpose
                      'is_deleted' => 1, // deleted
                  ],
                  [
                      'name' => 'Honeybee Works 🍯',
                      'alias' => '🍯-honeybee', // emoji-leading alias on purpose
                      'is_deleted' => 0,
                  ],
              ];

        $table = $this->table('companies');
        $table->insert($data)->saveData();
    }
}
