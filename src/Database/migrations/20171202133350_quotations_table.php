<?php

use Phinx\Migration\AbstractMigration;

class QuotationsTable extends AbstractMigration
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

        $this->table("quotations")
            ->addColumn("object", "string", ["limit"=>255])
            ->addColumn("join", "text", ["limit"=>\Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->addColumn("validity_deadline", "datetime")
            ->addColumn("deadline", "datetime")
            ->addColumn("created_at", "datetime")
            ->addColumn("updated_at", "datetime")
            ->addColumn("id_individual", "integer", [
                "null"=>true
            ])
            ->addColumn("id_professionnal", "integer", [
                "null"=>true
            ])
            ->create();

    }
}
