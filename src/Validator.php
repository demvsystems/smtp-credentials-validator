<?php

namespace Demv\SmtpValidator;

/**
 * Class Validator
 *
 * Uses the Connector class to validate user credentials.
 *
 * @package Demv\SmtpValidator
 */
class Validator
{
    const REPLY_CODES_BAD_CREDENTIALS = [530, 535, 554];

    /**
     * @param ConnectorInterface $connector
     * @param string             $username
     * @param string             $password
     *
     * @return bool
     */
    public function isValid(ConnectorInterface $connector, string $username, string $password): bool
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
