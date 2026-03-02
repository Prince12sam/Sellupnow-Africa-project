<?php
$html = file_get_contents(__DIR__ . '/../../adminlogin.html');
if (!$html) $html = file_get_contents(__DIR__ . '/../adminlogin.html');
if (!$html) $html = file_get_contents('adminlogin.html');
if (!$html) { echo "NO_HTML\n"; exit(1); }
if (preg_match('/name="_token" value="([^"]+)"/m', $html, $m)) {
    echo $m[1];
} else {
    // try meta tag
    if (preg_match('/meta name="csrf-token" content="([^"]+)"/m', $html, $m2)) {
        echo $m2[1];
    } else {
        echo "NO_TOKEN";
    }
}
