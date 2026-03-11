<?php
require "/home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin/vendor/autoload.php";
$app = require "/home/sellupnow/htdocs/www.sellupnow.com/sellupnow-admin/bootstrap/app.php";
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$json = json_encode(["loginType" => 4, "email" => "test@sellupnow.com", "password" => "Test@123", "fcmToken" => "test"]);
$request = Illuminate\Http\Request::create("/api/client/user/loginOrSignupUser", "POST", [], [], [], ["CONTENT_TYPE" => "application/json", "HTTP_ACCEPT" => "application/json", "HTTP_KEY" => "a9A0cGQYMZTlRviK4BkHWbDpeIdoFLXN8qxzOJhn67SjVrCf"], $json);
$response = $kernel->handle($request);
echo $response->getContent() . "\n";
echo "Status: " . $response->getStatusCode() . "\n";
$kernel->terminate($request, $response);
