<?php

namespace Demv\SmtpCredentialsValidator;

class Validator
{
    const REPLY_CODES_BAD_CREDENTIALS = ['530', '535', '554'];

    /**
     * @param Connector $connector
     * @param string    $username
     * @param string    $password
     *
     * @return bool
     */
    public function isValid(Connector $connector, string $username, string $password): bool
    {
        $connector->send('AUTH LOGIN');
        if ($connector->getReplyCode() !== 334) {
            return false;
        }

        $connector->send(base64_encode($username));
        if ($connector->getReplyCode() !== 334) {
            return false;
        }

        $connector->send(base64_encode($password));
        if ($connector->getReplyCode() !== 235) {
            return false;
        }

        return true;
    }
}
