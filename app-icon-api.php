<?php
// app-icon-api.php

// Set headers for images
header('Content-Type: image/png'); 
header('Access-Control-Allow-Origin: *');
header('Cache-Control: max-age=86400, public'); // Cache 24 hours

// Get package ID
$packageId = isset($_GET['id']) ? $_GET['id'] : '';
if (empty($packageId)) {
    die('Missing package ID');
}

// Create cache directory
$cacheDir = 'api_cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// Check cache
$cacheFile = $cacheDir . '/' . $packageId . '.png';
if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 86400)) {
    // Serve from cache
    readfile($cacheFile);
    exit;
}

// RapidAPI credentials
$rapidApiKey = '30186755c2msh02639d89f5c19a5p1b87fejsn590da5ff8808'; // API key của bạn

// API request to RapidAPI
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://google-play-store-scraper-api.p.rapidapi.com/app-details",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'language' => 'en',
        'country' => 'us',
        'appID' => $packageId  // Sửa lại để sử dụng biến trực tiếp
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "x-rapidapi-host: google-play-store-scraper-api.p.rapidapi.com",
        "x-rapidapi-key: $rapidApiKey"  // Sử dụng biến trực tiếp
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

// Log errors for debugging (bạn có thể xóa sau khi hoạt động ổn định)
if ($err) {
    error_log("cURL Error: " . $err);
}

// Check for errors
if ($err) {
    // Serve default image in case of error
    header('HTTP/1.1 500 Internal Server Error');
    readfile('error_icon.png');
    exit;
}

// Parse JSON response
$appData = json_decode($response, true);

// Ghi log response để debug (xóa trong phiên bản final)
error_log("API Response: " . substr($response, 0, 500) . "...");

// Kiểm tra response có đúng cấu trúc không
if (isset($appData['data']) && isset($appData['data']['icon'])) {
    // Lấy URL biểu tượng từ response
    $iconUrl = $appData['data']['icon'];
    
    // Download the icon
    $iconData = file_get_contents($iconUrl);
    
    if ($iconData) {
        // Save to cache
        file_put_contents($cacheFile, $iconData);
        
        // Output the icon
        echo $iconData;
        exit;
    } else {
        error_log("Failed to download icon from URL: $iconUrl");
    }
} else {
    error_log("API returned invalid response structure for package: $packageId");
    if (isset($appData['status'])) {
        error_log("API Status: " . $appData['status']);
    }
}

// Fallback to error icon
header('HTTP/1.1 404 Not Found');
readfile('error_icon.png');
?>