<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
.container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
.header { background: #2c3e50; color: #fff; padding: 28px 32px; }
.header h1 { margin: 0; font-size: 1.4rem; }
.body { padding: 28px 32px; color: #333; line-height: 1.6; }
.status-approved { background: #d4edda; color: #155724; border-radius: 6px; padding: 14px 18px; margin: 18px 0; }
.status-rejected { background: #f8d7da; color: #721c24; border-radius: 6px; padding: 14px 18px; margin: 18px 0; }
.footer { background: #f9f9f9; border-top: 1px solid #eee; padding: 16px 32px; font-size: 0.8rem; color: #999; }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
    </div>
    <div class="body">
        <p>Hi {{ $sellerName }},</p>

        @if($approved)
        <div class="status-approved">
            <strong>✅ Your video is approved and is now live in the Reels feed!</strong>
        </div>
        <p>Great news! The video for your listing <strong>"{{ $listingTitle }}"</strong> has been approved and is now live in the Reels feed for buyers to discover.</p>
        @else
        <div class="status-rejected">
            <strong>❌ Your video was not approved.</strong>
        </div>
        <p>Unfortunately, the video for your listing <strong>"{{ $listingTitle }}"</strong> could not be approved at this time.</p>
        @if($rejectReason)
        <p><strong>Reason:</strong> {{ $rejectReason }}</p>
        @endif
        <p>You are welcome to update your listing with a new video URL and resubmit for review.</p>
        @endif

        <p>Thank you for using {{ config('app.name') }}.</p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
