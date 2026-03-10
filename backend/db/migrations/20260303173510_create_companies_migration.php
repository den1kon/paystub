<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateCompaniesMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('companies', [
            'collation' => "utf8mb4_unicode_ci",
            'encoding' => "utf8mb4"
        ]);

        $table
            ->addTimestamps()
            ->addColumn('name', 'string', ['limit' => 60, 'null' => false])
            ->addColumn('alias', 'string', ['limit' => 40, 'null' => true])
            ->addColumn('is_deleted', 'boolean', ['default' => 0])
            ->addIndex('name', ['unique' => true])
            ->create();
    }
}
