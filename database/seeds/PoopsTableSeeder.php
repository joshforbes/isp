<?php

use App\Poop;
use Illuminate\Database\Seeder;

class PoopsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Poop::class, 50)->create();
    }
}
