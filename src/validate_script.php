<?php
require_once('Connector.php');
require_once('Validator.php');

set_time_limit(5);

$email    = '';
$host     = 'test.smtp.org';
$username = '';
$password = '';
$port     = 587;


$connector = new Connector($host, $port);
$connected = $connector->open();
echo ('initial response: ' . $connector->getResponse());
if ($connector->getReplyCode() !== 220) {
    exit;
}

$connector->send('EHLO ' . $host);
echo ('EHLO response: ' . $connector->getResponse());
if ($connector->getReplyCode() !== 250) {
    exit;
}

// fixme: auth login funktioniert mit starttls nicht
/*
$connector->send('STARTTLS');
echo ('TLS response: ' . $connector->getResponse());
if ($connector->getReplyCode() !== 220) {
    exit;
}
*/

$connector->send('AUTH LOGIN');
echo ('AUTH response: ' . $connector->getResponse());
if ($connector->getReplyCode() !== 334) {
    exit;
}

// ...