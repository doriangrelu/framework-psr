<?php

use Phinx\Seed\AbstractSeed;

class Individuals extends AbstractSeed
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
        $utility= new \Framework\Utility\Objects\Utility();
        $users=$this->table("individual");
        $data=[];
        $faker=\Faker\Factory::create("fr_FR");
        for($i=0;$i<100;$i++)
        {
            $date=$faker->dateTime("now");
            $data[]=[
                "first_name"=>$faker->firstName,
                "last_name"=>$faker->lastName,
                "id_users"=>12,
                "created_at"=>$date->format("Y-m-d H:i:s"),
                "updated_at"=>$date->format("Y-m-d H:i:s")
            ];
        }
        $users->insert($data)->save();
        $users=$this->table("professionnal");
        $data=[];
        $faker=\Faker\Factory::create("fr_FR");
        for($i=0;$i<50;$i++)
        {
            $date=$faker->dateTime("now");
            $data[]=[
                "first_name"=>$faker->firstName,
                "last_name"=>$faker->lastName,
                "siret"=>"65452125632145",
                "compagny_name"=>$faker->company,
                "id_users"=>12,
                "created_at"=>$date->format("Y-m-d H:i:s"),
                "updated_at"=>$date->format("Y-m-d H:i:s")
            ];
        }
        $users->insert($data)->save();
    }
}
