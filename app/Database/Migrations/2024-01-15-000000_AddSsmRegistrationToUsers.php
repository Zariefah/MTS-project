<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSsmRegistrationToUsers extends Migration {
  public function up() {
    $this->forge->addColumn('users', [
      'ssm_registration' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => true,
        'after' => 'displayname'
      ]
    ]);
  }

  public function down() {
    $this->forge->dropColumn('users', 'ssm_registration');
  }
}