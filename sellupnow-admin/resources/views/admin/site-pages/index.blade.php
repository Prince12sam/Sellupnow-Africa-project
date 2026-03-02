@extends('layouts.app')
@section('header-title', __('Website Pages'))
@section('content')
<div class="container-fluid my-4">
    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between px-1 mb-3">
        <h4 class="m-0">{{ __('Website Pages') }}</h4>
        @hasPermission('admin.sitePages.create')
            <a href="{{ route('admin.sitePages.create') }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus me-1"></i>{{ __('New Page') }}
            </a>
        @endhasPermission
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table border-left-right mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Title') }}</th>
                            <th>{{ __('Slug / URL') }}</th>
                            <th>{{ __('Content Type') }}</th>
                            <th class="text-center">{{ __('Status') }}</th>
                            <th class="text-center">{{ __('Last Updated') }}</th>
                            <th class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $i => $page)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $page->title }}</td>
                            <td>
                                <code>/{{ $page->slug }}</code>
                            </td>
                            <td>
                                @if($page->page_builder_status === 'on')
                                    <span class="badge bg-warning text-dark" title="Uses visual page-builder blocks; HTML content below is supplementary">Page Builder</span>
                                @else
                                    <span class="badge bg-info">HTML Content</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $page->status === 'publish' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($page->status) }}
                                </span>
                            </td>
                            <td class="text-center text-muted" style="font-size:.85em">{{ $page->updated_at }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    @if($page->page_builder_status === 'on')
                                        @hasPermission('admin.pageBuilders.edit')
                                            <a href="{{ route('admin.pageBuilders.page', $page->id) }}"
                                                class="btn btn-sm btn-primary" title="{{ __('Edit Content Blocks') }}">
                                                <i class="fa-solid fa-cubes me-1"></i>Content Blocks
                                            </a>
                                        @endhasPermission
                                    @endif
                                    @hasPermission('admin.sitePages.edit')
                                        <a href="{{ route('admin.sitePages.edit', $page->id) }}"
                                            class="btn btn-sm btn-outline-secondary" title="{{ __('Edit HTML / Settings') }}">
                                            <i class="fa-solid fa-file-pen"></i>
                                        </a>
                                    @endhasPermission
                                    @hasPermission('admin.sitePages.destroy')
                                        <form method="POST" action="{{ route('admin.sitePages.destroy', $page->id) }}"
                                              onsubmit="return confirm('{{ __('Delete this page?') }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endhasPermission
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="alert alert-info mt-3 mb-0 small">
        <i class="fa-solid fa-circle-info me-1"></i>
        <strong>Page Builder pages</strong> (About, FAQ, Blog, etc.) use visual widget blocks stored separately.
        The HTML content editor here edits the <em>page_content</em> field which is shown <strong>in addition to</strong> those blocks.
        For plain HTML pages (Privacy Policy, Terms, Safety Informations, Contact) the editor controls 100% of the displayed content.
    </div>
</div>
@endsection
