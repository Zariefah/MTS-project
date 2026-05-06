<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderDesignMaterialImages extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('orders')) {
            return;
        }

        $forge = \Config\Database::forge();

        $fields = [];
        if (!$this->db->fieldExists('design_images', 'orders')) {
            $fields['design_images'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }
        if (!$this->db->fieldExists('material_images', 'orders')) {
            $fields['material_images'] = [
                'type' => 'TEXT',
                'null' => true,
            ];
        }

        if (!empty($fields)) {
            $forge->addColumn('orders', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('orders')) {
            return;
        }

        $forge = \Config\Database::forge();
        if ($this->db->fieldExists('design_images', 'orders')) {
            $forge->dropColumn('orders', 'design_images');
        }
        if ($this->db->fieldExists('material_images', 'orders')) {
            $forge->dropColumn('orders', 'material_images');
        }
    }
}

