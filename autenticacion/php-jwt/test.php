#!/usr/bin/env php
<?php
require 'vendor/autoload.php';

use \Firebase\JWT\JWT;

$token_payload = [
  'Carrera' => 'ITI-10-3',
  'Nombre' => 'Oliver Sinue Burgara Estrella',
  'Correo' => 'oliver.burgara@gmail.com'
];

// This is your client secret
$key = '__test_secret__';

// This is your id token
$jwt = JWT::encode($token_payload, base64_decode(strtr($key, '-_', '+/')), 'HS256');

print "JWT:\n";
print_r($jwt);

$decoded = JWT::decode($jwt, base64_decode(strtr($key, '-_', '+/')), ['HS256']);

print "\n\n";
print "Decoded:\n";
print_r($decoded);

?>
