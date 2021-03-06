<?php

namespace Demv\SmtpValidator;

/**
 * Interface ConnectorInterface
 * @package Demv\SmtpValidator
 */
interface ConnectorInterface
{
    /**
     * @return int
     */
    public function getReplyCode(): int;

    /**
     * @return string
     */
    public function getResponse(): string;

    /**
     * @return bool
     */
    public function isEncrypted(): bool;

    /**
     * @return bool
     */
    public function open(): bool;

    /**
     * @return bool
     */
    public function close(): bool;

    /**
     * @return bool
     */
    public function startTls(): bool;

    /**
     * @param string $msg
     */
    public function send(string $msg): void;
}
