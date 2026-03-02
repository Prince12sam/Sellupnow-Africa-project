<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function srcUrl(): Attribute
    {
        $image = asset('default/default.jpg');

        // Media uploads are stored on the `public` disk.
        if ($this->src && Storage::disk('public')->exists($this->src)) {
            $image = Storage::disk('public')->url($this->src);
        }

        return Attribute::make(
            get: fn () => $image,
        );
    }
}
