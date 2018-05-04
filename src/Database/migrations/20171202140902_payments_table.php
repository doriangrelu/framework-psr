<?php

use Phinx\Migration\AbstractMigration;

class PaymentsTable extends AbstractMigration
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
        $this->table("payments")
            ->addColumn("id_payments_type", "integer")
            ->addColumn("id_quotations", "integer")
            ->addColumn("amount", "integer")
            ->addColumn("rank", "integer")
            ->addForeignKey("id_payments_type", "payments_type", "id")
            ->addForeignKey("id_quotations", "quotations", "id")
            ->create();
    }
}
