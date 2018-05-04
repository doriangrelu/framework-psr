<?php

use Phinx\Migration\AbstractMigration;

class CheckConstraintsNew extends AbstractMigration
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
        $this->table("lines")
            ->dropForeignKey("id_quotations")
            ->dropForeignKey("id_unity_type")
            ->addForeignKey("id_quotations", "quotations", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->addForeignKey("id_unity_type", "unity_type", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->update();
    }
}
