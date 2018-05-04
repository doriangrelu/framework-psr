<?php

use Phinx\Migration\AbstractMigration;

class AlterAttributesOfTables extends AbstractMigration
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
        $this->table("professionnal")
            ->changeColumn("birthday", "datetime", ["null"=>true])
            ->changeColumn("phone", "string", ["null"=>true, "limit"=>10])
            ->changeColumn("adress", "string", ["null"=>true, "limit"=>255])
            ->changeColumn("cp", "string", ["null"=>true, "limit"=>5])
            ->changeColumn("city", "string", ["null"=>true, "limit"=>255])

            ->update();
        $this->table("individual")
            ->changeColumn("birthday", "datetime", ["null"=>true])
            ->changeColumn("phone", "string", ["null"=>true, "limit"=>10])
            ->changeColumn("adress", "string", ["null"=>true, "limit"=>255])
            ->changeColumn("cp", "string", ["null"=>true, "limit"=>5])
            ->changeColumn("city", "string", ["null"=>true, "limit"=>255])
            ->addColumn("created_at", "datetime")
            ->addColumn("updated_at", "datetime")
            ->update();
        $this->table("quotations")
            ->addColumn("id_users", "integer")
            ->addForeignKey("id_users", "users", "id")
            ->update();
    }
}
