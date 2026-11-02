<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CheckJabatanColumn extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldData('users');
        foreach ($fields as $field) {
            if ($field->name === 'jabatan') {
                log_message('error', 'JABATAN TYPE: ' . $field->type . ' - MAX LENGTH: ' . $field->max_length);
            }
        }
    }

    public function down()
    {
    }
}
