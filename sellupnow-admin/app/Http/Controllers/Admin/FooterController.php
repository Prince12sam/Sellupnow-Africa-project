<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use App\Models\FooterItem;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FooterController extends Controller
{
    public function index()
    {
        $footers = Footer::with('items')->OrderBy('order')->get();

        $menus = Menu::OrderBy('order')->get();
        $pages = Page::where('is_active', true)->get();
        $disableItems = FooterItem::where('is_active', false)->get();

        // Listocean footer bridge (Listocean renders footer via widgets, not this admin footer builder)
        // This is best-effort: the secondary DB/app may be missing in some deployments.
        $customerWebUrl = rtrim(env('CUSTOMER_WEB_URL', 'http://127.0.0.1:8090'), '/');
        $listoceanFooterLogoUrl = null;
        $listoceanFooterContact = [];
        $listoceanCopyrightText = '';
        try {
            $listoceanFooterLogoUrl = $this->getListoceanFooterLogoUrl($customerWebUrl);
            $listoceanFooterContact = $this->getListoceanFooterContactInfo();
            $listoceanCopyrightText = $this->getListoceanCopyrightText();
        } catch (\Throwable $e) {
            // swallow: page should still load even if Listocean is offline
        }

        return view('admin.footer.index', compact(
            'footers',
            'menus',
            'pages',
            'disableItems',
            'listoceanFooterLogoUrl',
            'listoceanFooterContact',
            'listoceanCopyrightText'
        ));
    }

    public function updateListoceanLogos(Request $request)
    {
        $request->validate([
            'listocean_footer_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        if (! $request->hasFile('listocean_footer_logo')) {
            return redirect()->route('admin.footer.index')->withSuccess(__('No logo selected'));
        }

        try {
            $footerLogoId = $this->storeListoceanMediaUpload($request->file('listocean_footer_logo'));
            $this->updateListoceanFooterContactInfoLogo((string) $footerLogoId);
            $this->clearListoceanCaches();
        } catch (\Throwable $e) {
                return redirect()->route('admin.footer.index')->withErrors([
                    'listocean_footer_logo' => __('Unable to update customer web footer logo right now.'),
            ]);
        }

            return redirect()->route('admin.footer.index')->withSuccess(__('Customer web footer logo updated successfully'));
    }

    public function updateListoceanFooterContent(Request $request)
    {
        $request->validate([
            'listocean_address' => 'nullable|string|max:500',
            'listocean_phone' => 'nullable|string|max:255',
            'listocean_email' => 'nullable|email|max:255',
            'listocean_copyright' => 'nullable|string|max:500',
        ]);

        $address = trim((string) $request->input('listocean_address'));
        $phone = trim((string) $request->input('listocean_phone'));
        $email = trim((string) $request->input('listocean_email'));
        $copyright = trim((string) $request->input('listocean_copyright'));

        try {
            $this->updateListoceanFooterContactInfoContent([
                'address' => $address,
                'phone' => $phone,
                'email' => $email,
            ]);

            if ($copyright !== '') {
                if (!str_contains($copyright, '{copy}') && !str_contains($copyright, '{year}')) {
                    $copyright = '{copy} {year} ' . $copyright;
                }
                $this->updateListoceanCopyrightText($copyright);
            }

            $this->clearListoceanCaches();
        } catch (\Throwable $e) {
            return redirect()->route('admin.footer.index')->withInput()->withErrors([
                    'listocean_footer' => __('Unable to update customer web footer content right now.'),
            ]);
        }

            return redirect()->route('admin.footer.index')->withSuccess(__('Customer web footer updated successfully'));
    }

    public function update(Request $request, Footer $footer)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $footer->update([
            'title' => $request->title,
        ]);

        return back()->withSuccess('updated successfully');
    }

    public function updateItem(Request $request, FooterItem $footerItem)
    {
        $request->validate([
            'title' => 'required',
        ]);

        $footerItem->update([
            'title' => $request->title,
            'url' => $request->url ?? $footerItem->url,
        ]);

        return back()->withSuccess('updated successfully');
    }

    public function sectionSort(Request $request)
    {
        foreach ($request->sorted_data ?? [] as $item) {
            $footer = Footer::find($item['id']);
            $footer->update([
                'order' => $item['position'],
            ]);
        }

        return $this->json('section sorted successfully', [], 200);
    }

    public function addedNew(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'type' => 'required',
            'position' => 'required',
            'section_id' => 'required',
        ]);

        if ($request->type == 'menu') {
            $menu = Menu::find($request->id);
            FooterItem::create([
                'footer_id' => $request->section_id,
                'type' => 'link',
                'title' => $menu->name,
                'url' => '/'.$menu->url,
                'order' => $request->position,
                'target' => $menu->target,
                'is_active' => true,
                'is_default' => false,
            ]);
        } elseif ($request->type == 'page') {
            $page = Page::find($request->id);
            FooterItem::create([
                'footer_id' => $request->section_id,
                'type' => 'link',
                'title' => $page->title,
                'url' => '/'.$page->url,
                'order' => $request->position,
                'target' => '_self',
                'is_active' => true,
                'is_default' => false,
            ]);
        } else {
            $footerItem = FooterItem::find($request->id);
            $footerItem->update([
                'footer_id' => $request->section_id,
                'is_active' => true,
                'order' => $request->position,
            ]);
        }

        return $this->json('added successfully', [], 200);
    }

    public function itemSort(Request $request)
    {
        foreach ($request->sorted_data ?? [] as $item) {

            if (! $item['id'] || ! $item['position'] || ! $item['section_id']) {
                continue;
            }

            $footerItem = FooterItem::find($item['id']);
            $footerItem->update([
                'order' => $item['position'],
                'footer_id' => $item['section_id'],
            ]);
        }

        return $this->json('section sorted successfully', [], 200);
    }

    public function disabled(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        FooterItem::find($request->id)?->update([
            'is_active' => false,
        ]);

        return $this->json('disabled successfully', [], 200);
    }

    public function destroy(FooterItem $footerItem)
    {
        if ($footerItem->is_default) {
            $footerItem->update([
                'is_active' => false,
            ]);

            return back()->withSuccess('disabled successfully');
        }

        $footerItem->delete();

        return back()->withSuccess('deleted successfully');
    }

    private function listocean()
    {
        return DB::connection('listocean');
    }

    private function decodeListoceanWidgetContent(string $raw): ?array
    {
        $clean = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $raw);
        $decoded = json_decode($clean ?? '', true);
        return is_array($decoded) ? $decoded : null;
    }

    private function getListoceanFooterContactInfoWidget()
    {
        return $this->listocean()
            ->table('widgets')
            ->where('widget_location', 'footer_one')
            ->where('widget_name', 'ContactInfoWidget')
            ->orderBy('id')
            ->first();
    }

    private function getListoceanFooterContactInfo(): array
    {
        $widget = $this->getListoceanFooterContactInfoWidget();
        if (! $widget || ! $widget->widget_content) {
            return [];
        }

        $decoded = $this->decodeListoceanWidgetContent((string) $widget->widget_content);
        return is_array($decoded) ? $decoded : [];
    }

    private function getListoceanFooterLogoUrl(string $customerWebUrl): ?string
    {
        $decoded = $this->getListoceanFooterContactInfo();
        $imageId = $decoded['image'] ?? null;
        if (! $imageId) {
            return null;
        }

        $path = $this->listocean()->table('media_uploads')->where('id', (int) $imageId)->value('path');
        if (! $path) {
            return null;
        }

        return $customerWebUrl . '/assets/uploads/media-uploader/' . ltrim($path, '/');
    }

    private function getListoceanCopyrightText(): string
    {
        $widget = $this->listocean()
            ->table('widgets')
            ->where('widget_location', 'copyright')
            ->where('widget_name', 'CopyrightText')
            ->orderBy('id')
            ->first();

        if (! $widget || ! $widget->widget_content) {
            return '';
        }

        $decoded = $this->decodeListoceanWidgetContent((string) $widget->widget_content);
        if (! is_array($decoded)) {
            return '';
        }

        return (string) ($decoded['title'] ?? '');
    }

    private function updateListoceanFooterContactInfoLogo(string $attachmentId): void
    {
        $widget = $this->getListoceanFooterContactInfoWidget();
        if (! $widget) {
            return;
        }

        $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
        if (! is_array($decoded)) {
            $decoded = [];
        }

        $decoded['image'] = (string) $attachmentId;

        $this->listocean()->table('widgets')->where('id', $widget->id)->update([
            'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    private function updateListoceanFooterContactInfoContent(array $fields): void
    {
        $widget = $this->getListoceanFooterContactInfoWidget();
        if (! $widget) {
            return;
        }

        $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
        if (! is_array($decoded)) {
            $decoded = [];
        }

        foreach (['address', 'phone', 'email'] as $key) {
            if (array_key_exists($key, $fields) && $fields[$key] !== '') {
                $decoded[$key] = (string) $fields[$key];
            }
        }

        $this->listocean()->table('widgets')->where('id', $widget->id)->update([
            'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    private function updateListoceanCopyrightText(string $title): void
    {
        $widget = $this->listocean()
            ->table('widgets')
            ->where('widget_location', 'copyright')
            ->where('widget_name', 'CopyrightText')
            ->orderBy('id')
            ->first();

        if (! $widget) {
            return;
        }

        $decoded = $this->decodeListoceanWidgetContent((string) ($widget->widget_content ?? ''));
        if (! is_array($decoded)) {
            $decoded = [];
        }

        $decoded['title'] = $title;

        $this->listocean()->table('widgets')->where('id', $widget->id)->update([
            'widget_content' => json_encode($decoded, JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);
    }

    private function storeListoceanMediaUpload($uploadedFile): int
    {
        $originalName = (string) $uploadedFile->getClientOriginalName();
        $extension = strtolower((string) $uploadedFile->getClientOriginalExtension());
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);

        $slug = Str::slug($baseName);
        if (! $slug) {
            $slug = 'footer-logo';
        }

        $timestamp = time();
        $fileName = $slug . $timestamp . ($extension ? ('.' . $extension) : '');

        $targetDir = env('LISTOCEAN_PUBLIC_PATH')
            ? rtrim(str_replace('\\', '/', env('LISTOCEAN_PUBLIC_PATH')), '/') . '/assets/uploads/media-uploader'
            : listocean_core_path('public/assets/uploads/media-uploader');
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0775, true);
        }

        $uploadedFile->move($targetDir, $fileName);

        $fullPath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        $bytes = File::size($fullPath);
        $size = $bytes < 1024 * 1024 ? number_format($bytes / 1024, 2) . ' KB' : number_format($bytes / (1024 * 1024), 2) . ' MB';

        $dimensions = null;
        try {
            $info = @getimagesize($fullPath);
            if (is_array($info) && isset($info[0], $info[1])) {
                $dimensions = $info[0] . ' x ' . $info[1] . ' pixels';
            }
        } catch (\Throwable $e) {
            $dimensions = null;
        }

        $now = now();

        return (int) $this->listocean()->table('media_uploads')->insertGetId([
            'title' => $originalName,
            'path' => $fileName,
            'alt' => null,
            'size' => $size,
            'dimensions' => $dimensions,
            'user_id' => null,
            'type' => 'admin',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function clearListoceanCaches(): void
    {
        $listoceanCore = listocean_core_path();
        $artisan = $listoceanCore . DIRECTORY_SEPARATOR . 'artisan';
        if (! File::exists($artisan)) {
            return;
        }

        $cmd = 'cd ' . escapeshellarg($listoceanCore) . ' && php artisan cache:clear && php artisan view:clear';
        @shell_exec($cmd);
    }
}
