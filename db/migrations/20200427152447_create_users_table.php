<?php

use Phinx\Migration\AbstractMigration;

class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $users = $this->table('users', ['id' => false, 'primary_key' => 'id']);
        $users
            ->addColumn('id',           'uuid',     ['default' => 'UUID()'])
            ->addColumn('active',       'boolean',  ['default' => true])
            ->addColumn('admin',        'boolean',  ['default' => false])
            ->addColumn('roles',        'string',   ['default' => '[]'])
            ->addColumn('email',        'string',   ['limit' => 128, 'null' => true])
            ->addColumn('google_id',    'string',   ['limit' => 64, 'null' => true])
            ->addColumn('github_id',    'string',   ['limit' => 64, 'null' => true])
            ->addColumn('updated_at',   'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('created_at',   'datetime', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex(['email', 'google_id', 'github_id'], ['unique' => true])
            ->create();
    }
}
