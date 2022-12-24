<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageManipulation extends Model
{
    use HasFactory;

    // protected $table = 'image_manipulations';
    const TYPE_RESIZE = 'resize';

    const UPDATED_AT = null;
    
    protected $table = 'image_manipunations';

    protected $fillable = [
        'name',
        'path',
        'type',
        'data',
        'output_path',
        'user_id',
        'album_id',
    ];
}
