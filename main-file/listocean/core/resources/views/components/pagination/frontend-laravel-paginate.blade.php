<!-- Pagination -->
<div class="row justify-content-center mt-4 mb-4">
    <div class="col-lg-12">
        <div class="pagination">
            @if($alldata->count() > 0)
                <div class="blog-pagination">
                    <div class="custom-pagination">
                        {!! $alldata->links() !!}
                    </div>
                </div>
            @else
                <div class="no-listings-message">
                    <h2 class="">{{ $title ?? '' }}</h2>
                </div>
            @endif
        </div>
    </div>
</div>
