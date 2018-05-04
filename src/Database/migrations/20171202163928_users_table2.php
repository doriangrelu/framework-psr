<?php

use Phinx\Migration\AbstractMigration;

class UsersTable2 extends AbstractMigration
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
            ->addColumn("password", "string", ["limit"=>255])
            ->addColumn("token", "string", ["limit"=>255, "null"=>true])
            ->renameColumn("bithrday", "birthday")
            ->update();
        $this->table("individual")
            ->addColumn("id_users", "integer")
            ->addForeignKey("id_users", "users", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->update();
        $this->table("professionnal")
            ->addColumn("id_users", "integer")
            ->addForeignKey("id_users", "users", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->update();

    }
}
