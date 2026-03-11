<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEntriesMigration extends AbstractMigration
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
        $table = $this->table('entries', [
            'collation' => "utf8mb4_unicode_ci",
            'encoding' => "utf8mb4"
        ]);

        $table
            /* ->addTimestamps() // Phinx makes updated_at nullable by default, which I don't want */
            ->addColumn('created_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'])
            ->addColumn('project_id', 'integer', ['null' => true, 'signed' => false])
            ->addColumn('started_at', 'timestamp', ['null' => false])
            ->addColumn('ended_at', 'timestamp', ['null' => false])
            ->addColumn('is_deleted', 'boolean', ['default' => 0])
            ->addIndex(['project_id', 'started_at'], ['unique' => true])
            ->addForeignKey('project_id', 'projects', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->create();
    }
}
