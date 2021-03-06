<?php

require __DIR__ . '/vendor/autoload.php';

use Demv\SmtpValidator\Connector;
use Demv\SmtpValidator\PromptHelper;
use Demv\SmtpValidator\Validator;

define('USE_TLS', true);

$host = 'SMTP.office365.com';
$port = 587;

$email    = PromptHelper::prompt('Enter Email: ');
$username = $email;
$password = PromptHelper::promptSilent();

$connector = new Connector($host, $port);
$connected = $connector->open();

$userDomain = substr($email, strrpos($email, '@') + 1);
$connector->send('EHLO ' . $userDomain);
echo('*** EHLO response ***' . PHP_EOL . $connector->getResponse() . PHP_EOL);
if ($connector->getReplyCode() !== 250) {
    exit;
}

if (USE_TLS) {
    if (!$connector->startTls()) {
        exit;
    }

    $connector->send('EHLO ' . $userDomain);
    echo('*** EHLO response after TLS ***' . PHP_EOL . $connector->getResponse() . PHP_EOL);
    if ($connector->getReplyCode() !== 250) {
        exit;
    }
}

$validator = new Validator();
$isValid   = $validator->isValid($connector, $username, $password);

if (!$isValid && in_array($connector->getReplyCode(), Validator::REPLY_CODES_BAD_CREDENTIALS)) {
    echo '!!! BAD credentials !!!' . PHP_EOL . $connector->getResponse();
} elseif (!$isValid) {
    echo '!!! could NOT validate credentials !!!' . PHP_EOL . $connector->getResponse();
} else {
    echo '*** VALID ***' . PHP_EOL . $connector->getResponse();
}

if (!$connector->close()) {
    echo 'connection could not be closed!' . PHP_EOL;
}