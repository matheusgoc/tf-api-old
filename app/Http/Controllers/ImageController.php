<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Product Image Controller
 *
 * @package App\Http\Controllers
 */
class ImageController extends Controller
{
    private $service;
    private $validation = [
        'head' => 'boolean',
        'active' => 'boolean',
        'image' => [
            'mimes:jpeg',
//                Rule::dimensions()
//                    ->minWidth(Image::DETAIL_WIDTH)
//                    ->minHeight(Image::DETAIL_HEIGHT)
//                    ->ratio(Image::RATIO)
        ]
    ];

    public function __construct(ImageService $service)
    {
        $this->authorizeResource(Image::class, 'image', ['except' => ['destroy']]);
        $this->service = $service;
    }

    /**
     * List all product images not associated with any product
     *
     * @return Response
     */
    public function index()
    {
        return response($this->service->getAll());
    }

    /**
     * Generate one or many product images
     *
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function store(Request $request)
    {
        array_unshift($this->validation['image'], 'required');
        $request->validate($this->validation);

        $image = $this->service->create($request->all(), $request->file('image'));

        return response($image);
    }

    /**
     * Retrieve a product image
     *
     * @param Image $image
     * @return Response
     */
    public function show(Image $image)
    {
        return response($this->service->get($image));
    }

    /**
     * Update a product image
     *
     * @param Request $request
     * @param Image $image
     * @return Response
     * @throws Throwable
     */
    public function update(Request $request, Image $image)
    {
        $this->authorize('update', $image);

        $request->validate($this->validation);

        $image = $this->service->update($image, $request->all(), $request->file('image'));

        return response($image);
    }

    /**
     * Remove one or more product image
     *
     * @param Request $request
     * @param $ids
     * @return Response
     * @throws Throwable
     */
    public function destroy(Request $request, $ids)
    {
        $this->authorize('delete', Image::class);

        $this->service->delete($ids);

        return response('', 204);
    }
}
