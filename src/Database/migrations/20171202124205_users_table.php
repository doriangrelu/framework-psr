<?php

use Phinx\Migration\AbstractMigration;

class UsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {

        $this->table("users")
            ->addColumn("first_name", "string")
            ->addColumn("last_name", "string")
            ->addColumn("bithrday", "datetime")
            ->addColumn("siret", "string", ["limit"=>14])
            ->addColumn("adress", "string", ["limit"=>255])
            ->addColumn("phone", "string", ["limit"=>10])
            ->addColumn("mail", "string", ["limit"=>255])
            ->addColumn("compagny_name", "string", ["limit"=>255])
            ->addColumn("city", "string", ["limit"=>255])
            ->addColumn("cp", "string", ["limit"=>5])
            ->addColumn("created_at", "datetime")
            ->addColumn("updated_at", "datetime")
            ->create();

    }
}
