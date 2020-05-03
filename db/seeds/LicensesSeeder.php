<?php

use Phinx\Seed\AbstractSeed;

class LicensesSeeder extends AbstractSeed
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
        $faker = Faker\Factory::create();
        $data = [];

        for ($i = 0; $i < 5; $i++) {
            $data[] = [
                'user_id'       => $faker->uuid,
                'app_id'        => $faker->uuid,
                'license'       => $faker->uuid,
                'variant'       => 'Free',
                'updated_at'    => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s'),
            ];
        }

        $this->table('licenses')->insert($data)->saveData();
    }
}
