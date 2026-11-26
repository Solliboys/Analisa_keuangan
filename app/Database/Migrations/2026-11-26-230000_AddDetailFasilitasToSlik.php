<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailFasilitasToSlik extends Migration
{
    public function up()
    {
        $fieldsSlik = [
            'suku_bunga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'angsuran',
            ],
            'jangka_waktu' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'suku_bunga',
            ],
            'tgl_akad' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'jangka_waktu',
            ],
            'jatuh_tempo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'tgl_akad',
            ],
            'tunggakan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => '0',
                'after'      => 'jatuh_tempo',
            ],
        ];

        foreach ($fieldsSlik as $col => $def) {
            if (!$this->db->fieldExists($col, 'slik')) {
                $this->forge->addColumn('slik', [$col => $def]);
            }
        }
    }

    public function down()
    {
        $cols = ['suku_bunga', 'jangka_waktu', 'tgl_akad', 'jatuh_tempo', 'tunggakan'];
        foreach ($cols as $col) {
            if ($this->db->fieldExists($col, 'slik')) {
                $this->forge->dropColumn('slik', $col);
            }
        }
    }
}
