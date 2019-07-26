<?php

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // United States
        $country = new Country();
        $country->id = Country::USA;
        $country->name = 'United States';
        $country->ddi = '1';
        $country->save();

        // Brazil
        $country = new Country();
        $country->id = Country::BRA;
        $country->name = 'Brazil';
        $country->ddi = '55';
        $country->save();
    }
}
