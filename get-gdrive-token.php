<?php
$clientId     = '293157634291-g107bol4sjmauokb5okmann95mdrmvuo.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri  = 'http://localhost';
$scope        = 'https://www.googleapis.com/auth/drive.file';

$params = http_build_query([
    'client_id'     => $clientId,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'scope'         => $scope,
    'access_type'   => 'offline',
    'prompt'        => 'consent',
]);

echo "\nBuka URL ini di browser:\n";
echo "https://accounts.google.com/o/oauth2/auth?" . $params . "\n\n";
echo "Setelah redirect ke localhost (error itu normal),\n";
echo "Copy SELURUH URL dari address bar browser, paste di sini:\n> ";

$input = trim(fgets(STDIN));

// Ekstrak 'code' dari URL atau pakai langsung jika bukan URL
if (strpos($input, 'http') === 0 || strpos($input, 'localhost') === 0) {
    parse_str(parse_url($input, PHP_URL_QUERY), $queryParams);
    $code = $queryParams['code'] ?? null;
} else {
    $code = $input;
}

if (!$code) {
    echo "\nGagal mengambil kode! Coba paste URL lengkap dari browser.\n";
    exit(1);
}

echo "\nMengambil token...\n";

$response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code'          => $code,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri'  => $redirectUri,
            'grant_type'    => 'authorization_code',
        ]),
        'ignore_errors' => true,
    ],
]));

$token = json_decode($response, true);

if (isset($token['refresh_token'])) {
    echo "\n==============================================\n";
    echo " BERHASIL! Salin 3 baris ini ke file .env:\n";
    echo "==============================================\n";
    echo "GOOGLE_DRIVE_CLIENT_ID=" . $clientId . "\n";
    echo "GOOGLE_DRIVE_CLIENT_SECRET=" . $clientSecret . "\n";
    echo "GOOGLE_DRIVE_REFRESH_TOKEN=" . $token['refresh_token'] . "\n";
    echo "==============================================\n";
} else {
    echo "\nGagal mendapatkan token!\n";
    echo "Response: " . json_encode($token, JSON_PRETTY_PRINT) . "\n";
}