<?php
$controllers = [
    'OrderController','ProductController','CustomerController','RiderController',
    'CouponController','FlashSaleController','VatTaxController','CurrencyController',
    'SubscriptionPlanController','WhatsAppChatController','ReviewsController',
    'MembershipFeatureController','DeliveryChargeController','ChatOversightController',
    'CommissionRuleController','BoostController','DashboardController',
    'ReportReasonController','MenuController','TicketIssueTypeController',
    'AdController','BannerController','BlogController',
];
foreach ($controllers as $c) {
    $path = __DIR__ . '/../app/Http/Controllers/Admin/' . $c . '.php';
    if (file_exists($path)) {
        $code = file_get_contents($path);
        $usesListocean = str_contains($code, 'listocean');
        $usesModels = preg_match('/use App\\\\Models\\\\(?!Frontend)(\w+)/', $code, $m) ? $m[1] : null;
        $db = $usesListocean ? '✅ listocean' : '❌ admin DB';
        $model = $usesModels ? " [model: $usesModels]" : '';
        echo "$c => $db$model\n";
    }
}
