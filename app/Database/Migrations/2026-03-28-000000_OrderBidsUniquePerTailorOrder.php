<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class OrderBidsUniquePerTailorOrder extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('order_bids')) {
            return;
        }

        // Enforce one bid per tailor per order at DB level; remove older duplicate rows first
        if ($this->db->DBDriver === 'MySQLi') {
            $this->db->query(
                'DELETE ob1 FROM order_bids ob1
                 INNER JOIN order_bids ob2
                   ON ob1.order_id = ob2.order_id AND ob1.tailor_id = ob2.tailor_id AND ob1.id > ob2.id'
            );
        }

        $idx = $this->db->query("SHOW INDEX FROM order_bids WHERE Key_name = 'order_bids_order_tailor_unique'")->getNumRows();
        if ($idx > 0) {
            return;
        }

        $this->db->query(
            'ALTER TABLE order_bids ADD UNIQUE INDEX order_bids_order_tailor_unique (order_id, tailor_id)'
        );
    }

    public function down()
    {
        if ($this->db->tableExists('order_bids')) {
            $this->db->query('ALTER TABLE order_bids DROP INDEX order_bids_order_tailor_unique');
        }
    }
}
