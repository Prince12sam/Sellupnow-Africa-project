@extends('frontend.layout.master')
@section('site-title')
    {{ $category_name->name }}
@endsection

@section('page-title')
    {{ $category_name->name }}
@endsection

@section('inner-title')
    {{ __('Category:') }}{{ $category_name->name }}
@endsection
@section('content')
    <!--Blog Details part-->
    <div class="blog-details-wraper section-padding2">
        <div class="container-1440">
            <x-breadcrumb.user-profile-breadcrumb
                :title="''"
                :innerTitle="__('Blogs')"
                :subInnerTitle="$category_name->name"
                :chidInnerTitle="''"
                :routeName="$all_blog_route"
                :subRouteName="'#'"
            />
                 <div class="row justify-content-end">
                    <div class="col-md-12">
                        <!--Daily Blog Part-->
                        <div class="daily-blog-part">
                            <div class="container-1440">
                                <div class="daily-blog-top-part">
                                    <h2 class="blog-heading">{{ __('Category:') }}  {{ $category_name->name }}</h2>
                                </div>
                            </div>
                            <div class="devider"></div>
                            <div class="blog-wraper">
                                <div class="container-1440">
                                    <div class="row g-4 grid">
                                        @foreach($all_blogs as $blog)
                                            <div class="col-md-3 catagory catagory{{$blog->category->id}}">
                                                <div class="blog-card">
                                                    <a href="{{ route('frontend.blog.single', $blog->slug ?? 'x') }}">
                                                        <div class="img">
                                                            {!! render_image_markup_by_attachment_id($blog->image,'','','thumb') !!}
                                                        </div>
                                                    </a>
                                                    <div class="text-part">
                                                        <div class="date">{{ optional($blog->created_at)->diffForHumans() }}</div>
                                                        <div class="title">
                                                            <a href="{{ route('frontend.blog.single', $blog->slug ?? 'x') }}">
                                                                {!! strlen($blog->title) > 55 ? substr($blog->title, 0, 55) . '...' : $blog->title !!}
                                                            </a>
                                                        </div>
                                                        <p class="pera">
                                                            {!! strlen(strip_tags($blog->blog_content)) > 80 ? substr(strip_tags($blog->blog_content), 0, 80) . '...' : $blog->blog_content !!}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <x-pagination.frontend-laravel-paginate :alldata="$all_blogs" :title="__('No Blog Yet')"/>
                    </div>
                </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){

                let description = document.getElementById('description');
                let showMoreButton = document.getElementById('showMoreButton');
                $('#showMoreButton').show();
                let isExpanded = false;
                let originalContent = description.textContent;
                if (description.textContent.length > 700) {
                    description.textContent = description.textContent.substring(0, 700) + '...';
                }else {
                    $('#showMoreButton').hide();
                }
                showMoreButton.addEventListener('click', function() {
                    if (!isExpanded) {
                        description.textContent = originalContent;
                        showMoreButton.textContent = 'Show Less';
                    } else {
                        description.textContent = description.textContent.substring(0, 700) + '...';
                        showMoreButton.textContent = 'Show More';
                    }
                    isExpanded = !isExpanded;
                });

            });
        })(jQuery);
    </script>
@endsection
