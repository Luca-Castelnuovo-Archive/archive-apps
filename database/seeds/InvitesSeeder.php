<?php

use CQ\DB\Seeder;

class InvitesSeeder extends Seeder
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = self::faker();
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'code' => $faker->md5,
                'expires_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->table('invites')->insert($data)->saveData();
    }
}
