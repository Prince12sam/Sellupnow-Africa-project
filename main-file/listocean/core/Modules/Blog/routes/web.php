<?php

use Illuminate\Support\Facades\Route;
use Modules\Blog\app\Http\Controllers\Backend\BlogController;
use Modules\Blog\app\Http\Controllers\Backend\BlogTagsController;
use Modules\Blog\app\Http\Controllers\Frontend\FrontendBlogController;


/*------------------ ADMIN MANAGE BLOG  --------------*/
// Admin routes
Route::group(['prefix' => 'admin/blog', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get('/all',[BlogController::class, 'index'])->name('admin.all.blog')->permission('blog-list');
    Route::get('/new',[BlogController::class, 'newBlog'])->name('admin.blog.new')->permission('blog-add');
    Route::post('/new',[BlogController::class, 'storeNewBlog']);
    Route::get('/get/tags',[BlogController::class, 'getTagsByAjax'])->name('admin.get.tags.by.ajax');
    Route::get('/edit/{id}',[BlogController::class, 'editBlog'])->name('admin.blog.edit')->permission('blog-edit');
    Route::post('/update/{id}',[BlogController::class, 'updateBlog'])->name('admin.blog.update');
    Route::post('/clone',[BlogController::class, 'cloneBlog'])->name('admin.blog.clone')->permission('blog-clone');
    Route::post('/delete/all/lang/{id}',[BlogController::class, 'deleteBlogAllLang'])->name('admin.blog.delete.all.lang');
    Route::post('/bulk-action',[BlogController::class, 'bulkActionBlog'])->name('admin.blog.bulk.action')->permission('blog-bulk-delete');
    Route::get('/blog-details-settings',[BlogController::class, 'blogDetailsSettings'])->name('admin.blog.details.settings')->permission('blog-settings');
    Route::post('/blog-details-settings-update',[BlogController::class, 'blogDetailsSettingsUpdate'])->name('admin.blog.details.settings.update');
    Route::get('/search',[BlogController::class, 'searchBlog'])->name('admin.blog.search');
    Route::get('/paginate',[BlogController::class, 'paginate'])->name('admin.blog.paginate');

    //Trashed & Restore
    Route::get('/trashed',[BlogController::class, 'trashedBlogs'])->name('admin.blog.trashed')->permission('blog-trashed-list');
    Route::get('/trashed/restore/{id}',[BlogController::class, 'restoreTrashedBlog'])->name('admin.blog.trashed.restore')->permission('blog-trashed-restore');
    Route::post('/trashed/delete/{id}',[BlogController::class, 'deleteTrashedBlog'])->name('admin.blog.trashed.delete')->permission('blog-trashed-delete');
    Route::post('/trashed/bulk-action',[BlogController::class, 'trashedBulkActionBlog'])->name('admin.blog.trashed.bulk.action')->permission('blog-trashed-bulk-delete');

    //Single Page Settings
    Route::get('/single-settings', [BlogController::class, 'blogSinglePageSettings'])->name('admin.blog.single.settings');
    Route::post('/single-settings', [BlogController::class, 'updateBlogSinglePageSettings']);

    //Others Page Settings
    Route::get('/others-settings', [BlogController::class, 'blogOthersPageSettings'])->name('admin.blog.others.settings');
    Route::post('/others-settings', [BlogController::class, 'updateBlogOthersPageSettings']);
    Route::post('/blog-approve', [BlogController::class, 'blogApprove'])->name('admin.blog.approve');
});

//BACKEND BLOG TAGS
Route::group(['prefix' => 'admin/tags', 'middleware' => ['auth:admin', 'setlang']], function () {
    Route::get('/',[BlogTagsController::class, 'index'])->name('admin.blog.tags')->permission('tag-list');
    Route::post('/store',[BlogTagsController::class, 'newTags'])->name('admin.blog.tags.store');
    Route::post('/update',[BlogTagsController::class, 'updateTags'])->name('admin.blog.tags.update');
    Route::post('/delete/all/lang/{id}',[BlogTagsController::class, 'deleteTagsAllLang'])->name('admin.blog.tags.delete.all.lang');
    Route::post('/bulk-action', [BlogTagsController::class, 'bulkAction'])->name('admin.blog.tags.bulk.action')->permission('tag-bulk-delete');
    Route::get('/search',[BlogTagsController::class, 'searchTags'])->name('admin.blog.tags.search');
    Route::get('/paginate',[BlogTagsController::class, 'paginateTag'])->name('admin.blog.tags.paginate');
});

// BLOG FRONTEND ROUTE LISTS
Route::group(['middleware' => ['globalVariable', 'maintains_mode','setlang']], function () {
    $blog_page_slug = getSlugFromReadingSetting('blog_page') ?? 'blog';
    Route::group(['prefix' => $blog_page_slug, 'namespace' => 'Frontend'], function () {
        Route::get('/{slug}', [FrontendBlogController::class, 'blog_single'])->name('frontend.blog.single');
        Route::post('/post-blog-comment', [FrontendBlogController::class, 'blog_comment'])->name('frontend.blog.comment');
        Route::get('/category/{slug?}', [FrontendBlogController::class, 'category_wise_blog_page'])->name('frontend.blog.category');
        Route::get('/tags/{tag_id?}', [FrontendBlogController::class, 'tags_wise_blog_page'])->name('frontend.blog.tags');
        Route::get('/{blog_id}/comments', [FrontendBlogController::class, 'loadMoreComments'])->name('frontend.blog.load.comments');
    });
});
