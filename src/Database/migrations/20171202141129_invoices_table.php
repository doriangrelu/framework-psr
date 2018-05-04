<?php

use Phinx\Migration\AbstractMigration;

class InvoicesTable extends AbstractMigration
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
        $this->table("invoices", ["id"=>false, "primary_key"=>["id_payments", "id_quotations"]])
            ->addColumn("id_payments", "integer")
            ->addColumn("id_quotations", "integer")
            ->addColumn("created_at", "datetime")
            ->addColumn("updated_at", "datetime")
            ->addColumn("payed", "boolean")
            ->addForeignKey("id_payments", "payments", "id")
            ->addForeignKey("id_quotations", "quotations", "id")
            ->create();
    }
}
