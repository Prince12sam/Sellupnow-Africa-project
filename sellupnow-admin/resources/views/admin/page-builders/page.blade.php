@extends('layouts.app')
@section('header-title', __('Content Blocks') . ' — ' . $page->title)
@section('content')
<div class="container-fluid my-4">

    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.sitePages.index') }}">Website Pages</a></li>
            <li class="breadcrumb-item active">{{ $page->title }} — Content Blocks</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <div>
            <h4 class="m-0">{{ $page->title }}</h4>
            <small class="text-muted">/<code>{{ $page->slug }}</code></small>
        </div>
        <div class="d-flex gap-2">
            @hasPermission('admin.sitePages.edit')
                <a href="{{ route('admin.sitePages.edit', $page->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-file-pen me-1"></i>Edit HTML Content (page_content)
                </a>
            @endhasPermission
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="alert alert-info small mb-3">
        <i class="fa-solid fa-circle-info me-1"></i>
        This page uses the <strong>Page Builder</strong>. Each card below is a <em>content block</em> rendered on the frontend.
        Click <strong>Edit Block</strong> to modify its content, then save — changes appear on
        <a href="#" target="_blank"><code>/{{ $page->slug }}</code></a>.
    </div>

    @if($blocks->isEmpty())
        <div class="alert alert-warning">
            No content blocks found for this page.
        </div>
    @else
        <div class="row g-3">
            @foreach($blocks as $block)
            @php
                $settings = $block->settings;
                $label  = $settings['title'] ?? $settings['subtitle'] ?? null;
                $icon   = match($block->addon_name) {
                    'TextEditor'               => 'fa-align-left',
                    'FaqOne', 'Faq'            => 'fa-question-circle',
                    'ContactInfo'              => 'fa-address-card',
                    'Membership'               => 'fa-id-badge',
                    'AboutUsOne', 'AboutUs'    => 'fa-users',
                    'TeamOne'                  => 'fa-people-group',
                    'BlogTipsOne', 'AllBlog'   => 'fa-newspaper',
                    'HeaderStyleOne'           => 'fa-image',
                    'ListingsOne'              => 'fa-list',
                    default                    => 'fa-cube',
                };
                $editableTypes = ['TextEditor','FaqOne','Faq','ContactInfo','Membership',
                    'AboutUsOne','AboutUs','TeamOne','WorkWithUsOne','EmpoweringOpportunitiesOne',
                    'HeaderStyleOne','ListingsOne','AllBlog','BlogTipsOne','MarketPlaceOne'];
                $isFully = in_array($block->addon_name, $editableTypes);
            @endphp
            <div class="col-md-6">
                <div class="card h-100 border {{ $isFully ? '' : 'border-dashed' }}">
                    <div class="card-body d-flex align-items-start gap-3">
                        <div class="text-primary" style="font-size:1.5rem;width:2rem;text-align:center">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $block->addon_name }}</h6>
                            @if($label)
                                <p class="text-muted small mb-1">{{ Str::limit($label, 70) }}</p>
                            @endif
                            @if(!empty($settings['text_editor']))
                                <p class="small text-muted mb-1">
                                    <i class="fa-solid fa-align-left me-1"></i>
                                    {{ Str::limit(strip_tags($settings['text_editor']), 80) }}
                                </p>
                            @endif
                            {{-- Show FAQ count --}}
                            @foreach($settings as $k => $v)
                                @if(is_array($v) && isset($v['title_']) && is_array($v['title_']))
                                    <p class="small text-muted mb-0"><i class="fa-solid fa-list me-1"></i>{{ count($v['title_']) }} item(s)</p>
                                @endif
                            @endforeach
                        </div>
                        <div>
                            @hasPermission('admin.pageBuilders.edit')
                                <a href="{{ route('admin.pageBuilders.edit', $block->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-pen me-1"></i>Edit Block
                                </a>
                            @endhasPermission
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
