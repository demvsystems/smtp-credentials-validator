<?php

require __DIR__ . '/vendor/autoload.php';

use Demv\SmtpCredentialsValidator\Connector;
use Demv\SmtpCredentialsValidator\Validator;

$email    = '';
$host     = 'smtp.googlemail.com';
$username = '';
$password = '';
$port     = 587;

$connector = new Connector($host, $port);
$connected = $connector->open();

$userDomain = explode('@', $email)[1] ?? '';
$connector->send('EHLO ' . $userDomain);
echo('*** EHLO response before TLS ***' . PHP_EOL . $connector->getResponse() . PHP_EOL);
if ($connector->getReplyCode() !== 250) {
    exit;
}

if (!$connector->startTls(STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
    exit;
}

$connector->send('EHLO ' . $userDomain);
echo('*** EHLO response after TLS ***' . PHP_EOL . $connector->getResponse() . PHP_EOL);
if ($connector->getReplyCode() !== 250) {
    exit;
}

$validator = new Validator();
if ($validator->isValid($connector, $username, $password)) {
    echo 'VALID!' . PHP_EOL;
} else {
    echo 'NOT VALID!' . PHP_EOL;
}

if (!$connector->close()) {
    echo 'connection could not be closed!' . PHP_EOL;
}