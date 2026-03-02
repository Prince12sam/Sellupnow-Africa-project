<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ListoceanPagesController extends Controller
{
    private function db()
    {
        // Prefer the dedicated 'listocean' connection when available (used in production),
        // but fall back to the default connection if it isn't configured or fails.
        try {
            if (config('database.connections.listocean')) {
                return DB::connection('listocean')->table('pages');
            }
        } catch (\Exception $e) {
            // ignore and fall back
        }

        return DB::table('pages');
    }

    public function index()
    {
        // Exclude structural/template pages that are fully auto-rendered
        // by ListOcean's own controllers and don't benefit from manual editing.
        $excludeSlugs = ['home-one', 'listings', 'blog', 'safety-informations'];

        $pages = $this->db()
            ->whereNotIn('slug', $excludeSlugs)
            ->orderByRaw("CASE status WHEN 'publish' THEN 0 ELSE 1 END")
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'status', 'page_builder_status', 'updated_at']);

        // Ensure essential site pages exist so administrators can edit them via the panel.
        // This is idempotent and will not overwrite existing pages.
        $required = [
            ['slug' => 'about', 'title' => 'About'],
            ['slug' => 'terms-and-conditions', 'title' => 'Terms & Conditions'],
            ['slug' => 'privacy-policy', 'title' => 'Privacy Policy'],
            ['slug' => 'contact', 'title' => 'Contact'],
            ['slug' => 'faq', 'title' => 'Faq'],
            ['slug' => 'help-and-support', 'title' => 'Help & support']
        ];

        foreach ($required as $r) {
            $exists = $this->db()->where('slug', $r['slug'])->exists();
            if (! $exists) {
                $this->db()->insert([
                    'title' => $r['title'],
                    'slug' => $r['slug'],
                    'page_content' => null,
                    'status' => 'publish',
                    'layout' => 'normal_layout',
                    'visibility' => 'all',
                    'breadcrumb_status' => 'on',
                    'navbar_variant' => '01',
                    'footer_variant' => '01',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Refresh pages list after ensuring required pages exist
        $pages = $this->db()
            ->whereNotIn('slug', $excludeSlugs)
            ->orderByRaw("CASE status WHEN 'publish' THEN 0 ELSE 1 END")
            ->orderBy('title')
            ->get(['id', 'title', 'slug', 'status', 'page_builder_status', 'updated_at']);

        return view('admin.site-pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.site-pages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'nullable|string|max:255',
            'page_content' => 'nullable|string',
            'status'       => 'required|in:publish,draft',
        ]);

        $slug = $request->slug
            ? Str::slug($request->slug)
            : Str::slug($request->title);

        $exists = DB::connection('listocean')
            ->table('pages')
            ->where('slug', $slug)
            ->exists();

        if ($exists) {
            return back()->withInput()->withErrors(['slug' => 'A page with this slug already exists.']);
        }

        $this->db()->insert([
            'title'        => $request->title,
            'slug'         => $slug,
            'page_content' => $request->page_content,
            'status'       => $request->status,
            'layout'       => 'normal_layout',
            'visibility'   => 'all',
            'breadcrumb_status' => 'on',
            'navbar_variant'    => '01',
            'footer_variant'    => '01',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.sitePages.index')
            ->with('success', 'Page created successfully.');
    }

    public function edit(int $id)
    {
        $page = $this->db()->where('id', $id)->first();
        if (! $page) {
            abort(404);
        }

        return view('admin.site-pages.edit', compact('page'));
    }

    public function editBySlug(string $slug)
    {
        $page = $this->db()->where('slug', $slug)->first();

        if (! $page) {
            $titles = [
                'about'                => 'About Us',
                'terms-and-conditions' => 'Terms & Conditions',
                'privacy-policy'       => 'Privacy Policy',
                'contact'              => 'Contact',
                'faq'                  => 'FAQ',
                'help-and-support'     => 'Help & Support',
            ];

            $this->db()->insert([
                'title'             => $titles[$slug] ?? ucwords(str_replace('-', ' ', $slug)),
                'slug'              => $slug,
                'page_content'      => null,
                'status'            => 'publish',
                'layout'            => 'normal_layout',
                'visibility'        => 'all',
                'breadcrumb_status' => 'on',
                'navbar_variant'    => '01',
                'footer_variant'    => '01',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $page = $this->db()->where('slug', $slug)->first();
        }

        return view('admin.site-pages.edit', compact('page'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'page_content' => 'nullable|string',
            'status'       => 'required|in:publish,draft',
        ]);

        $page = $this->db()->where('id', $id)->first();
        if (! $page) {
            abort(404);
        }

        $this->db()->where('id', $id)->update([
            'title'        => $request->title,
            'page_content' => $request->page_content,
            'status'       => $request->status,
            'updated_at'   => now(),
        ]);

        return redirect()->route('admin.sitePages.index')
            ->with('success', 'Page updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->db()->where('id', $id)->delete();

        return back()->with('success', 'Page deleted.');
    }
}
