<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Class CategoryController
 *
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    private $service;
    private $validation = [
        'name' => 'required',
        'name_pt' => 'required',
        'position' => 'integer|between:1,127',
        'active' => 'boolean'
    ];

    public function __construct(CategoryService $service)
    {
        $this->authorizeResource(Category::class, 'category');
        $this->service = $service;
    }

    /**
     * List all categories
     *
     * @param Request $request
     * @return Response
     * @throws AuthorizationException
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);

        $categoryId = $request->get('category_id');

        return response($this->service->getAll($categoryId));
    }

    /**
     * Create new category
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function store(Request $request)
    {
        $request->validate(array_merge($this->validation, [
            'category_id' => 'exists:categories',
        ]));

        $category = $this->service->create($request->all());

        return response($category, 201);
    }

    /**
     * Retrieve a category
     *
     * @param Category $category
     * @return Response
     */
    public function show(Category $category)
    {
        return response($this->service->get($category));
    }

    /**
     * Update a category
     *
     * @param Request $request
     * @param Category $category
     * @return Response
     */
    public function update(Request $request, Category $category)
    {
        $request->validate(array_merge($this->validation, [
            'category_id' => [
                Rule::exists('categories')->whereNot('id',  $category->id)
            ]
        ]));

        $category = $this->service->update($category, $request->all());

        return response($category);
    }

    /**
     * Remove a category
     *
     * @param Category $category
     * @return Response
     * @throws Throwable
     */
    public function destroy(Category $category)
    {
        $this->service->delete($category);

        return response('', 204);
    }
}
