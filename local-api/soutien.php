<?php

// =========================
// 🔒 CONFIG PROD
// =========================

ini_set('display_errors', 0);
error_reporting(0);

// =========================
// 🌐 CORS
// =========================

header('Access-Control-Allow-Origin: https://thegoodme.fr');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit;
}

// =========================
// 🔑 ENV
// =========================

$envPath = __DIR__ . '/.env';

if (!file_exists($envPath)) {
  http_response_code(500);
  echo json_encode(['error' => 'config']);
  exit;
}

$env = parse_ini_file($envPath);

$api_key = $env['OPENAI_API_KEY'] ?? null;
$mode = $env['APP_MODE'] ?? 'maintenance';

// =========================
// 🧠 MODE MAINTENANCE
// =========================

if ($mode !== 'live') {
  echo json_encode([
    'choices' => [
      [
        'message' => [
          'content' => "The Good Me est en maintenance 🌿 Reviens plus tard."
        ]
      ]
    ]
  ]);
  exit;
}

// =========================
// 📩 INPUT
// =========================

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data) || !isset($data['actions']) || !is_array($data['actions'])) {
  http_response_code(400);
  exit;
}

// 🔒 limite actions
$actions = array_slice($data['actions'], 0, 5);

// 🔒 limite taille
foreach ($actions as &$a) {
  $a = substr(trim($a), 0, 200);
}

if (empty($actions)) {
  http_response_code(400);
  exit;
}

// =========================
// 🛡️ IDENTIFICATION
// =========================

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$secret_salt = 'thegoodme_secure_2026';
$hashedId = hash('sha256', $ip . $secret_salt);

// =========================
// 📊 LIMITES
// =========================

$today = date('Y-m-d');
$logDir = __DIR__ . '/logs';
$logFile = "$logDir/boost_log_$today.json";

if (!is_dir($logDir)) {
  mkdir($logDir, 0775, true);
}

// 🔒 lock fichier
$fp = fopen($logFile, 'c+');
flock($fp, LOCK_EX);

$logContent = stream_get_contents($fp);
$log = $logContent ? json_decode($logContent, true) : [];

if (!is_array($log)) {
  $log = [];
}

$userData = $log[$hashedId] ?? ['count' => 0, 'last' => 0];

$now = time();

// ⏱ cooldown (modifiable facilement)
$COOLDOWN = 300; // 5 min

if ($now - $userData['last'] < $COOLDOWN) {
  http_response_code(429);
  echo json_encode([
    'choices' => [
      [
        'message' => [
          'content' => "Prends un moment 🙂 Tu peux revenir dans quelques minutes."
        ]
      ]
    ]
  ]);
  exit;
}

// 📅 quota
$LIMIT = 3;

if ($userData['count'] >= $LIMIT) {
  http_response_code(429);
  echo json_encode([
    'choices' => [
      [
        'message' => [
          'content' => "Tu as déjà utilisé tes boosts aujourd’hui 🌿"
        ]
      ]
    ]
  ]);
  exit;
}

// update
$userData['count'] += 1;
$userData['last'] = $now;
$log[$hashedId] = $userData;

// write
ftruncate($fp, 0);
rewind($fp);
fwrite($fp, json_encode($log));
flock($fp, LOCK_UN);
fclose($fp);

// =========================
// ✍️ PROMPT
// =========================

$prompt = "Voici ce que j’ai fait récemment : " . implode(', ', $actions) . ".

Tu es une voix humaine, chaleureuse et sincère.
Encourage-moi de façon naturelle et motivante.

130 à 180 mots max.";

// =========================
// 🤖 OPENAI
// =========================

$payload = [
  'model' => 'gpt-3.5-turbo',
  'messages' => [
    ['role' => 'system', 'content' => 'Tu es bienveillant.'],
    ['role' => 'user', 'content' => $prompt]
  ],
  'max_tokens' => 200,
  'temperature' => 0.9
];

$ch = curl_init('https://api.openai.com/v1/chat/completions');

curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
  ],
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);
$curlError = curl_errno($ch);
curl_close($ch);

if ($response === false || $curlError) {
  http_response_code(500);
  exit;
}

echo $response;
