<?php

require __DIR__ . '/vendor/autoload.php';

use Demv\SmtpCredentialsValidator\Connector;
use Demv\SmtpCredentialsValidator\Validator;

define('USE_TLS', false);

$host = 'test.smtp.org';
$port = 587;

$email    = 'bit-bucket@test.smtp.org';
$username = 'user04';
$password = 'pass04';

$connector = new Connector($host, $port);
$connected = $connector->open();

$userDomain = explode('@', $email)[1] ?? '';
$connector->send('EHLO ' . $userDomain);
echo('*** EHLO response before TLS ***' . PHP_EOL . $connector->getResponse() . PHP_EOL);
if ($connector->getReplyCode() !== 250) {
    exit;
}

if (USE_TLS && !$connector->startTls()) {
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