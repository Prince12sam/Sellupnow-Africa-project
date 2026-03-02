@if(!empty($reviews))
    @foreach($reviews as $key => $review)
        @php
            if($reviewtype == 'received'){
                  $reviewer_info = \App\Models\User::find($review->reviewer_id);
            }else{
                 $reviewer_info = \App\Models\User::find($review->user_id);
            }

            $isLastReview = $key === count($user->reviews) - 1;
        @endphp
        @if($reviewer_info)
            <div class="single-reviews">
                <div class="single-review-top d-flex justify-content-between align-items-end">
                    <div class="reviewer d-flex align-items-center">
                        <div class="seller-img">
                            {!! render_image_markup_by_attachment_id($reviewer_info->image, '') !!}
                        </div>
                        <div class="name-rating">
                            <div class="rating">
                                @if($review->rating >= 1)
                                    <b>{!! ratting_star(round($review->rating, 1)) !!} </b>
                                @endif
                            </div>
                            <div class="name">{{ $reviewer_info->fullname }}</div>
                        </div>
                    </div>
                    <div class="date">
                        @if($review->created_at)
                            {{ \Carbon\Carbon::parse($review->created_at)->format('d, M, Y') }}
                        @endif
                    </div>
                </div>
                <div class="review-text">
                    {{ $review->message }}
                </div>
            </div>
            @if(!$isLastReview)
                <div class="devider"></div>
            @endif
        @endif
    @endforeach
@endif
