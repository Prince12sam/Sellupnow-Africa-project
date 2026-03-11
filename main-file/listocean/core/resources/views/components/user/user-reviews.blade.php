@if(!empty($reviews) && count($reviews) > 0)
    @foreach($reviews as $key => $review)
        @php
            if($reviewtype == 'received'){
                  $reviewer_info = \App\Models\User::find($review->reviewer_id);
            }else{
                 $reviewer_info = \App\Models\User::find($review->reviewer_id);
            }

            $isLastReview = $key === count($reviews) - 1;
        @endphp
        @if($reviewer_info)
            <div class="single-reviews">
                <div class="single-review-top d-flex justify-content-between align-items-end">
                    <div class="reviewer d-flex align-items-center">
                        <div class="seller-img">
                            @if(!empty($reviewer_info->image))
                                {!! render_image_markup_by_attachment_id($reviewer_info->image, '') !!}
                            @else
                                <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#fe2c55,#ff6b35);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#fff;">{{ strtoupper(substr($reviewer_info->fullname ?? 'U', 0, 1)) }}</div>
                            @endif
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
@else
    <div style="text-align:center;padding:50px 20px;color:#94a3b8;">
        <svg width="56" height="56" fill="none" viewBox="0 0 24 24" style="opacity:.3;margin-bottom:10px;"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="#94a3b8" stroke-width="1.5" stroke-linejoin="round"/></svg>
        <p style="font-size:15px;margin:0;">{{ __('No reviews yet. Be the first to review this seller!') }}</p>
    </div>
@endif
