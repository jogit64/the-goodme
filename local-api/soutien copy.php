<?php
// 🔧 Affichage des erreurs en dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🛡️ CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  exit;
}

header('Content-Type: application/json');

// 📦 Lecture du .env
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
  http_response_code(500);
  echo json_encode(['error' => 'Fichier .env introuvable']);
  exit;
}
$env = parse_ini_file($envPath);
$api_key = $env['OPENAI_API_KEY'] ?? null;
if (!$api_key) {
  http_response_code(500);
  echo json_encode(['error' => 'Clé API manquante']);
  exit;
}

// 📩 Lecture des données envoyées
$input = file_get_contents('php://input');
$data = json_decode($input, true);
$actions = $data['actions'] ?? [];
$rawDeviceId = $data['deviceId'] ?? $_SERVER['REMOTE_ADDR'];

// 🔐 Hachage du deviceId avec sel
$secret_salt = '9cbd5f89e5ad44bcb6f8e65fd17f_thegoodme_@2025';

$hashedId = hash('sha256', $rawDeviceId . $secret_salt);

// 📅 Fichier de log local par jour
$today = date('Y-m-d');
$logDir = __DIR__ . '/logs';
$logFile = "$logDir/boost_log_$today.json";

// 🗂 Créer dossier s’il n’existe pas
if (!is_dir($logDir)) {
  mkdir($logDir, 0775, true);
}

// 🔄 Lecture ou init du log
$log = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

// 🔢 Limite : 3 appels / jour
$log[$hashedId] = ($log[$hashedId] ?? 0) + 1;

if ($log[$hashedId] > 3) {
  http_response_code(429);
  echo json_encode(['error' => 'Tu as atteint ta limite de 3 boosts pour aujourd’hui.']);
  exit;
}

// 💾 Sauvegarde du log
file_put_contents($logFile, json_encode($log));

// 🚨 Validation côté PHP aussi
if (empty($actions)) {
  http_response_code(400);
  echo json_encode(['error' => 'Aucune action fournie']);
  exit;
}

// ✍️ Prompt
$prompt = "Voici ce que j’ai fait récemment : " . implode(', ', $actions) . ".

Tu es une voix humaine, présente, chaleureuse et vivante – comme un ami qui me connaît bien et me soutient avec sincérité.
Tu vas m’écrire un message de soutien positif et motivant, à partir de ces actions.

Ta mission :
- Commence directement par une réaction engageante, comme : “Bravo !”, “Super !”, ou une vraie phrase enthousiaste.
- Utilise un ton naturel, vivant, complice. Tu peux t’adresser à moi en “tu”, comme si tu me parlais vraiment.
- Mets en avant au moins 3 de mes actions de manière concrète, comme des exemples.
- Dis pourquoi ces gestes sont importants, ce qu’ils disent de moi.
- Pas de flatterie creuse ni de blabla abstrait : reste humain, vrai, positif.
- Termine sur une phrase simple et chaleureuse, comme une présence à mes côtés.

Pas de langage formel, pas de métaphores floues, pas de mise en forme spéciale, pas de style thérapeute ou spirituel.

Tu écris 130 à 180 mots maximum. Le message doit être fluide, chaleureux, encourageant, et me donner envie de continuer dans ce que je fais.";

// 🔁 Appel OpenAI
$payload = [
  'model' => 'gpt-3.5-turbo',
  'messages' => [
    ['role' => 'system', 'content' => 'Tu es une voix calme et bienveillante.'],
    ['role' => 'user', 'content' => $prompt]
  ],
  'max_tokens' => 350,
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
  CURLOPT_CAINFO => realpath(__DIR__ . '/cacert.pem'),
]);

$response = curl_exec($ch);
$curlError = curl_errno($ch) ? curl_error($ch) : null;
curl_close($ch);

// 🪵 Log pour debug
file_put_contents(__DIR__ . '/debug.log', print_r([
  'input' => $input,
  'hashedId' => $hashedId,
  'count_today' => $log[$hashedId],
  'response' => $response,
  'curlError' => $curlError ?? 'aucune'
], true));

// 🚨 Si erreur
if ($response === false || $curlError) {
  http_response_code(500);
  echo json_encode(['error' => $curlError ? "Erreur cURL : $curlError" : "Erreur lors de l’appel à l’API OpenAI"]);
  exit;
}

// ✅ Succès
echo $response;
