<?php

// Generate key pairs for client and server
$clientKeypair = sodium_crypto_box_keypair();
$serverKeypair = sodium_crypto_box_keypair();

// Extract public and secret keys for client
$clientPublicKey = sodium_crypto_box_publickey($clientKeypair);
$clientSecretKey = sodium_crypto_box_secretkey($clientKeypair);

// Extract public and secret keys for server
$serverPublicKey = sodium_crypto_box_publickey($serverKeypair);
$serverSecretKey = sodium_crypto_box_secretkey($serverKeypair);

// Encode keys in base64 format
$clientPublicKeyBase64 = base64_encode($clientPublicKey);
$clientSecretKeyBase64 = base64_encode($clientSecretKey);
$serverPublicKeyBase64 = base64_encode($serverPublicKey);
$serverSecretKeyBase64 = base64_encode($serverSecretKey);

// Display the keys and nonce
echo "Client Public Key: " . $clientPublicKeyBase64 . PHP_EOL;
echo "Client Secret Key: " . $clientSecretKeyBase64 . PHP_EOL;
echo "Server Public Key: " . $serverPublicKeyBase64 . PHP_EOL;
echo "Server Secret Key: " . $serverSecretKeyBase64 . PHP_EOL;
