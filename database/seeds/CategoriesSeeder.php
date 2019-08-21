<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shoesTypes = [
            ['Flats', 'Sapatilhas'],
        ];

        $this->createCategories($shoesTypes);
    }

    private function createCategories($shoesTypes) {

        DB::transaction(function () use ($shoesTypes) {

            $shoesCategory = new Category();
            $shoesCategory->name = 'Shoes';
            $shoesCategory->name_pt = 'Calçados';
            $shoesCategory->position = 1;
            $shoesCategory->save();

            foreach ($shoesTypes as $key=>$type) {

                $category = new Category();
                $category->name = $type[0];
                $category->name_pt = $type[1];
                $category->category_id = $shoesCategory->id;
                $category->position = $key + 1;
                $category->save();
            }
        });
    }
}
