<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as InterventionImage;
use Throwable;
use Exception;

/**
 * Product Image Service
 *
 * @package App\Services
 */
class ImageService
{
    private $path = '';
    private $paths = [];

    /**
     * Retrieve all product images not associated with any product
     *
     * @return LengthAwarePaginator
     */
    public function getAll()
    {
        return Image::whereNull('product_id')->paginate();
    }

    /**
     * Retrieve product image
     *
     * @param Image $pi
     * @return Image
     */
    public function get(Image $pi)
    {
        return $pi;
    }

    /**
     * Create a product image
     *
     * @param array $data
     * @param UploadedFile $file
     * @return Image
     * @throws Throwable
     */
    public function create(array $data, UploadedFile $file)
    {
        return $this->save($data, $file);
    }

    /**
     * Update a product image
     *
     * @param UploadedFile $file
     * @param array $data
     * @param Image $image
     * @return Image
     * @throws Throwable
     */
    public function update(Image $image, array $data = [], UploadedFile $file = null)
    {
        $oldFiles = [];
        if ($file) {

            // set old files to remove after update the image
            $oldFiles = [
                $image->detail,
                $image->list,
                $image->thumb
            ];
        }

        // update image
        $image = $this->save($data, $file, $image);

        // remove old files
        Storage::delete($oldFiles);

        return $image;
    }

    /**
     * Save or create a product image
     *
     * @param array $data
     * @param UploadedFile $file
     * @param Image $image
     *
     * @return Image
     * @throws Throwable
     */
    private function save(array $data = [], UploadedFile $file = null, Image $image = null)
    {
        try {

            if ($file) {

                // handle product image
                $this->path = $file->store('products');
                $this->handleImage();
            }

            // update product image
            $image = ($image)?: new Image();
            foreach($this->paths as $type=>$path) {
                $image->{$type} = $path;
            }
            $image->fill($data);
            $image->save();

            return $image;

        } catch (Exception $ex) {

            if ($file) {
                $this->removeImages();
            }

            throw $ex;
        }
    }

    /**
     * Remove one or more product image not associated with any product
     *
     * @param $ids
     * @throws Throwable
     */
    public function delete($ids)
    {
        $ids = explode(',', $ids);

        $filesToRemove = [];
        $images = Image::whereNull('product_id')->whereIn('id', $ids)->get();

        DB::beginTransaction();
        try {

            foreach ($images as $image) {

                array_push($filesToRemove, $image->detail, $image->list, $image->thumb);
                $image->delete();
            }

            Storage::delete($filesToRemove);

            DB::commit();

        } catch (Exception $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Generate formatted images
     *
     * @param $path
     * @return array
     */
    private function handleImage($path = '')
    {
        $path = ($path)?: $this->path;

        $image = InterventionImage::make(Storage::path($path));

        // define the file names
        $ext = [];
        $pattern = "/\.\w+$/";
        preg_match($pattern, $path, $ext);
        $ext = $ext[0];
        $path = preg_split($pattern, $path)[0];
        $this->paths = [

            'detail' => $path . '_detail' . $ext,
            'list' => $path . '_list' . $ext,
            'thumb' => $path . '_thumb' . $ext
        ];

        // generate resized files
        $image->resize(Image::DETAIL_WIDTH, Image::DETAIL_HEIGHT)
            ->save(Storage::path($this->paths['detail']));

        $image->resize(Image::LIST_WIDTH, Image::LIST_HEIGHT)
            ->save(Storage::path($this->paths['list']));

        $image->resize(Image::THUMB_WIDTH, Image::THUMB_HEIGHT)
            ->save(Storage::path($this->paths['thumb']));

        Storage::delete($path . $ext);
        $this->path = '';

        return $this->paths;
    }

    /**
     * Delete the generated product images
     */
    private function removeImages()
    {
        if ($this->path) {

            Storage::delete($this->path);
        }

        if ($this->paths) {

            Storage::delete($this->paths);
        }
    }
}
