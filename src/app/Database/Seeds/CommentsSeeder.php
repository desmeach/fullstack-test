<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CommentsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'salam@gmail.com',
                'text'    => 'Random comment from seed 1',
                'date'    => '05.03.2023',
            ],
            [
                'name' => 'vader@gmail.com',
                'text'    => 'Random comment from seed 2',
                'date'    => '05.01.2023',
            ],
            [
                'name' => 'booba@mail.ru',
                'text'    => 'Random comment from seed 4',
                'date'    => '05.04.2023',
            ],
            [
                'name' => 'beba@mail.ru',
                'text'    => 'Random comment from seed 5',
                'date'    => '05.03.2024',
            ],
            [
                'name' => 'baba@mail.ru',
                'text'    => 'Random comment from seed 6',
                'date'    => '30.01.2024',
            ],
        ];

        foreach ($data as $row) {
            $this->db->table('comments')->insert($row);
        }
    }
}
