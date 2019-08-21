<?php

namespace App\Services;

use App\Models\Category;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Category Service
 *
 * @package App\Services
 */
class CategoryService
{
    /**
     * Retrieve a list of categories
     *
     * @param $categoryId
     * @return LengthAwarePaginator
     */
    public function getAll($categoryId = null)
    {
        $query = Category::where('active', false);

        if (is_null($categoryId)) {

            $query->where('category_id', $categoryId);

        } elseif (!$categoryId) {

            $query->whereNull('category_id');
        }

        return $query->orderBy('position')->paginate();
    }

    /**
     * Retrieve a category
     *
     * @param Category $category
     * @return Category
     */
    public function get(Category $category)
    {
        return $category;
    }

    /**
     * Create new category
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data)
    {
        return Category::create($data);
    }

    /**
     * Update a category
     *
     * @param Category $category
     * @param $data
     * @return Category
     */
    public function update(Category $category, $data)
    {
        $category->update($data);

        return $category;
    }

    /**
     * Delete a category
     *
     * @param Category $category
     * @return boolean
     * @throws Exception
     */
    public function delete(Category $category)
    {
        return $category->delete();
    }
}
