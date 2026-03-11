<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

class ImagePicker extends Component
{
    public string $name;
    public bool $multiple;
    public string $imagePath;
    public string|null $value;

    /**
     * Create a new component instance.
     */

    public function __construct(string $name, bool $multiple = false, string|null $value = null)
    {
        $this->name = $name;
        $this->multiple = $multiple;
        $this->value = $value;

        $imagePath = asset('default/default.jpg');

        if ($value) {
            if (preg_match('/^https?:\/\//i', $value)) {
                // Already a full URL
                $imagePath = $value;
            } elseif (str_starts_with($value, '/storage/') || str_starts_with($value, 'storage/')) {
                // Storage-relative URL
                $imagePath = url('/' . ltrim($value, '/'));
            } elseif (ctype_digit(trim($value))) {
                // Numeric Listocean attachment ID — look up in media_uploads
                $imagePath = $this->resolveListoceanAttachmentUrl((int) $value) ?? $imagePath;
            } elseif (Storage::disk('public')->exists($value)) {
                // Storage-relative path
                $imagePath = Storage::disk('public')->url($value);
            }
        }
        $this->imagePath = $imagePath;
    }

    /**
     * Resolve a Listocean media_uploads attachment ID to a full URL.
     */
    private function resolveListoceanAttachmentUrl(int $id): ?string
    {
        try {
            $path = DB::connection('listocean')
                ->table('media_uploads')
                ->where('id', $id)
                ->value('path');

            if (! $path) {
                return null;
            }

            return Storage::disk('listocean_media')->url((string) $path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image-picker');
    }
}

