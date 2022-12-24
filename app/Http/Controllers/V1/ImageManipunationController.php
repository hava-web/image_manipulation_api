<?php

namespace App\Http\Controllers\V1;

use App\Models\Album;
use Illuminate\Support\Str;
use App\Models\ImageManipulation;
use Illuminate\Http\UploadedFile;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use App\Http\Requests\StoreImageManupulationRequest;
use App\Http\Requests\UpdateImageManupulationRequest;
use App\Http\Resources\V1\ImageManipulationResource;

class ImageManipunationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function getByAlbum(Album $album)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreImageManupulationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function resize(StoreImageManupulationRequest $request)
    {
        $all = $request->all();

        $image = $all['image'];
        unset($all['image']);
        $data = [
            'type' => ImageManipulation::TYPE_RESIZE,
            'data' => json_encode($all),
            'user_id' => null,

        ];

        if(isset($all['album_id']))
        {
            $data['album_id'] = $all['album_id'];
        }

        $dir = 'images/'.Str::random().'/';
        $absolutePath = public_path($dir);
        File::makeDirectory($absolutePath);

        if($image instanceof UploadedFile)
        {
            $data['name'] = $image->getClientOriginalName();
            //test.jpg -> test-resize.jpg
            $filename = pathinfo($data['name'],PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $originalPath = $absolutePath.$data['name'];

            $image->move($absolutePath, $data['name']);
            $data['path'] = $dir.$data['name'];
        }
        else
        {
            $data['name'] = pathinfo($image,PATHINFO_BASENAME);
            $filename = pathinfo($image, PATHINFO_FILENAME);
            $extension = pathinfo($image, PATHINFO_EXTENSION);
            $originalPath = $absolutePath.$data['name'];
            
            copy($image,$absolutePath.$data['name']);
        }
        $data['path'] = $dir.$data['name'];

        $w = $all['w'];
        $h = $all['h'] ?? false;

        list($width, $height, $image) = $this->getImageWidthAndHeight($w, $h, $originalPath);

        $resizeFilename = $filename.'-resize.'.$extension;  

        $image->resize($width, $height)->save($absolutePath.$resizeFilename);
        $data['output_path'] = $dir.$resizeFilename;

        $imageManupulation = ImageManipulation::create($data);
        return new ImageManipulationResource($imageManupulation);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImageManupulation  $imageManupulation
     * @return \Illuminate\Http\Response
     */
    public function show(ImageManipulation $imageManupulation)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImageManupulation  $imageManupulation
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageManipulation $imageManupulation)
    {
        //
    }

    protected function getImageWidthAndHeight($w, $h, string $originalPath)
    {
        $image = Image::make($originalPath);
        $originalWidth = $image->width();
        $originalHeight = $image->height();

        if(str_ends_with($w,'%'))
        {
            $ratioW = (float)str_replace('%', '', $w);
            $ratioH = $h ? (float)str_replace('%', '', $h) : $ratioW;

            $newWidth = $originalWidth * $ratioW/ 100;
            $newHeight = $originalHeight * $ratioH/ 100;
        }
        else
        {
            $newWidth = (float)$w;
            $newHeight = $h ?  (float)$h : $originalHeight * $newWidth/$originalWidth;
        }

        return [$newWidth, $newHeight, $image];
    }
}
