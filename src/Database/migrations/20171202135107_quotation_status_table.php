<?php

use Phinx\Migration\AbstractMigration;

class QuotationStatusTable extends AbstractMigration
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
        $this->table("quotation_status", ["id"=>false, "primary_key"=>["id_status", "id_quotations"]])
            ->addColumn("id_status", "integer")
            ->addColumn("id_quotations", "integer")
            ->addForeignKey("id_status", "status", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->addForeignKey("id_quotations", "quotations", "id", [
                "delete"=>"cascade",
                "update"=>"cascade"
            ])
            ->create();
    }
}
