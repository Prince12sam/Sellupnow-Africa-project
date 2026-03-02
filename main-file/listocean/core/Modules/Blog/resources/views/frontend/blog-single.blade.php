@extends('frontend.layout.master')
@section('site-title')
    {{ $blog_post->title }}
@endsection
@section('page-title')
    <?php
    $page_info = request()->url();
    $str = explode("/",request()->url());
    $page_info = $str[count($str)-2];
    ?>
    {{ __(ucwords(str_replace("-", " ", $page_info))) }}
@endsection
@section('inner-title')
    {{ $blog_post->title}}
@endsection
@section('page-meta-data')
    {!!  render_page_meta_data($blog_post) !!}
@endsection
@section('style')
    <style>
        .single-add-image {
            width: 150px;
            height: 100%;
            flex-shrink: 0;
        }
        @media only screen and (max-width: 1299.98px) {
            .single-add-image {
                width: 100px!important;
            }
            .more-blog-head a{
                font-size: 14px!important;
            }
        }

        @media only screen and (max-width: 1199.98px) {
            .blog_more_single_image{
                flex-direction: column;
            }
            .single-add-image {
                width: 100%!important;
            }
        }
    </style>
@endsection
@section('content')
    <!--Blog Details part-->
    <div class="blog-details-wraper section-padding2">
        <div class="container-1440">
            <x-breadcrumb.user-profile-breadcrumb
                :title="''"
                :innerTitle="__('Blogs')"
                :subInnerTitle="__('Blog Details')"
                :chidInnerTitle="''"
                :routeName="$all_blog_route"
                :subRouteName="'#'"
            />
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="blog-main-img">
                        {!! render_image_markup_by_attachment_id($blog_post->image,'','thumb') !!}
                    </div>
                    <div class="blog-text-wraper">
                        <div class="main-tag">
                            <a href="{{ route('frontend.blog.category',optional($blog_post->category)->slug) }}">
                                <i class="las la-tag"></i>
                                {{ optional($blog_post->category)->name }}
                            </a>
                        </div>
                        <h3 class="blog-header mb-3">
                            <a href="{{ route('frontend.blog.single', $blog_post->slug) }}"> {{ $blog_post->title }} </a>
                        </h3>
                        <div class="writter-part">
                            <div class="text">
                                <div class="date">
                                    <i class="las la-clock"></i>
                                    {{ optional($blog_post->created_at)->diffForHumans() }}
                                </div>
                            </div>
                        </div>

                        <p class="pera" id="description">
                            {!! Str::limit(str_replace('&nbsp;', ' ', strip_tags($blog_post->blog_content)), 30000) !!}
                        </p>
                        <button id="showMoreButton" class="show-more-btn">{{ __('Show More') }}</button>

                    </div>
                    <div class="bottom-addon-part">
                        <div class="share">
                            <div class="text">{{ get_static_option('blog_share_title') ?? __('Share:') }}</div>
                            <div class="icon">
                                {!! single_post_share(route('frontend.blog.single',['id'=>$blog_post->id, 'slug'=> $blog_post->slug]),$blog_post->title,$blog_post->image) !!}
                            </div>
                        </div>
                        <div class="tags-wraper">
                            {{ get_static_option('blog_tag_title') ?? '' }}
                            @foreach (optional($blog_post)->tags as $tag)
                               <a href="{{ route('frontend.blog.tags', $tag->id) }}" class="tag">{{ $tag->name }}</a>
                            @endforeach
                        </div>
                        @if(Auth::guard('web')->check())
                            <form action="#" class="blog_comment_form write-comment" method="post">
                                @csrf
                                <input type="hidden" value="{{ $blog_post->id }}" name="blog_id" id="blog_id">
                                <textarea name="message" id="message" class="w-100 box-shadow1 p-24" placeholder="{{ get_static_option('blog_comment_message_title') ?? __('Write a comment') }}"></textarea>
                                <button type="submit" class="submit-btn">{{ get_static_option('blog_comment_button_title') ?? __('Post Comment') }}</button>
                            </form>
                        @else
                            <div class="btn-wrapper">
                            @if(empty(get_static_option('disable_user_otp_verify')))
                                <button class="cmn-btn4" href="{{ route('user.login').'?return='.request()->path()}}">{{__('Sign in for comment')}}</button>
                            @else
                                <button class="cmn-btn4" data-bs-toggle="modal" data-bs-target="#loginModal">{{ __('Sign in for comment') }}</button>
                            @endif
                            </div>
                        @endif
                    </div>
                    @if($blog_post->comments?->count() > 2)
                        <div id="commentsContainer"></div>
                        <button id="loadMoreCommentsBtn" class="new-cmn-btn browse-ads  mt-5 new_load_more_comments">{{ get_static_option('blog_comment_load_more_title') ?? __('Load More Comments') }}</button>
                    @endif
                </div>
                <div class="col-lg-4">
                    <div class="more-blog-sidebar">
                        <h3 class="blog-header">{{ get_static_option('related_blog_title') ?? __('Related Blog') }} </h3>
                        <div class="more-blog-wraper">
                            @if(!empty($related_blog))
                                @foreach($related_blog as $related)
                                    <div class="more-single-blog box-shadow1 p-24">
                                       <div class="d-flex gap-3 blog_more_single_image">
                                           <div class="single-add-image mb-2">
                                               <a href="{{ route('frontend.blog.single',$related->slug) }}">
                                                   {!! render_image_markup_by_attachment_id($related->image, '', ' thumb') !!}
                                               </a>
                                           </div>
                                           <div class="more-blog-head">
                                               <a href="{{ route('frontend.blog.single',$related->slug) }}">{{ $related->title }}</a>
                                           </div>
                                       </div>
                                        <div class="divider"></div>
                                        <div class="writter-part">
                                            <div class="text">
                                                <div class="date"><i class="las la-clock"></i>{{ optional($related->created_at)->diffForHumans() }}</div>
                                                <div class="name">
                                                    <a href="{{ route('frontend.blog.category',optional($related->category)->slug) }}">
                                                     <i class="las la-tags"></i>{{ optional($related->category)->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-frontend.login/>
@endsection
@section('scripts')
    <script>
        (function($){
            "use strict";

            $(document).ready(function(){
                // if click load more button show comments
                $(document).on('click', '#loadMoreCommentsBtn', function() {
                    let offset = $('.comment-show-contents').length;
                    loadComments(offset + 5);
                });

                $('.new_load_more_comments').trigger('click');

                // if page scroll auto load comments
                let scroll_check = false;
                $(window).scroll(function() {
                    let threshold = 100;
                    if (!scroll_check && $(window).scrollTop() + $(window).height() >= $(document).height() - threshold) {
                        scroll_check = true;
                        let offset = $('.comment-show-contents').length;
                        loadComments(offset + 2);
                    }
                });

                function loadComments(offset) {
                    let blog_id = '{{ $blog_post->id }}';
                    $.ajax({
                        url: '{{ route("frontend.blog.load.comments", ["blog_id" => $blog_post->id]) }}',
                        type: 'GET',
                        data: { offset: offset },
                        success: function(response) {
                            let comments = response.comments;
                            if (comments.length > 0) {
                                // loaded comments
                                $.each(comments, function(index, comment) {
                                    let commentHtml = '<div class="comment-show-contents padding-top-30">';
                                    commentHtml += '<div class="about-seller-flex-content style-03">';
                                    commentHtml += '<div class="about-seller-thumb"><a href="javascript:void(0)">';
                                    // Render user image markup
                                    if (comment.user_image) {
                                        commentHtml += '<button class="seller-img p-0">';
                                        commentHtml += '' + comment.user_image + '';
                                        commentHtml += '</button>';
                                    } else {
                                        commentHtml += '<img src="default-image-url.jpg" alt="Default Image">';
                                    }
                                    commentHtml += '</a></div>';
                                    commentHtml += '<div class="about-seller-content">';
                                    commentHtml += '<h5 class="title"><a href="javascript:void(0)">' + comment.name + '</a></h5>';
                                    commentHtml += '<p class="about-review-para">' + comment.message + '</p>';
                                    commentHtml += '<i class="las la-clock">';
                                    commentHtml += '<span class="date">' + comment.created_at + '</span>';
                                    commentHtml += '</div></div></div>';
                                    $('#commentsContainer').append(commentHtml);
                                });
                            } else {
                                $('#loadMoreCommentsBtn').hide();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }


                $(document).on('submit','.blog_comment_form',function(e){
                    e.preventDefault();
                    let blog_id = $('#blog_id').val();
                    let name = $('#name').val();
                    let email = $('#email').val();
                    let message = $('#message').val();

                    $.ajax({
                        url:"{{ route('frontend.blog.comment') }}",
                        method:"post",
                        data:{
                            blog_id:blog_id,
                            name:name,
                            email:email,
                            message:message,
                        },
                        success:function(res){
                            if (res.status == 'success') {
                                toastr.options = {
                                    "closeButton": true,
                                    "debug": false,
                                    "newestOnTop": false,
                                    "progressBar": true,
                                    "preventDuplicates": true,
                                    "onclick": null,
                                    "showDuration": "100",
                                    "hideDuration": "1000",
                                    "timeOut": "5000",
                                    "extendedTimeOut": "1000",
                                    "showEasing": "swing",
                                    "hideEasing": "linear",
                                    "showMethod": "show",
                                    "hideMethod": "hide"
                                };
                                toastr.success('Success!! Thanks For Comments---');
                                $('.blog_comment_form')[0].reset();

                            }else if (res.status == 'validation_error') {
                                let errorMessage = '';
                                for (let fieldName in res.errors) {
                                    errorMessage += res.errors[fieldName] + '<br>';
                                }
                                toastr.error(errorMessage);
                            } else if(res.status == 'error_auth') {
                                toastr.error(res.error);
                            }else {
                                console.error('Unexpected response:', res);
                            }

                        }
                    });
                });


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
