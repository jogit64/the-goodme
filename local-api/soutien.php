<?php
http_response_code(503);
header('Content-Type: application/json');

echo json_encode([
  "error" => "Service temporairement indisponible",
  "message" => "The Good Me est en maintenance. Pour plus d'infos : johannr.fr"
]);
exit;
