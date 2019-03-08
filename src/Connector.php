<?php

class Connector
{
    const RESPONSE_LENGTH = 4096;
    const TIMEOUT         = 5;

    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $response;
    /**
     * @var resource
     */
    private $connection;
    /**
     * @var int
     */
    private $errno;
    /**
     * @var string
     */
    private $errstr;

    /**
     * Connector constructor.
     *
     * @param string $host
     * @param int    $port
     */
    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        $this->connection = fsockopen($this->host, $this->port, $this->errno, $this->errstr, self::TIMEOUT);
        if ($this->errno || !$this->connection) {
            return false;
        }

        $this->updateResponse();

        return true;
    }

    /**
     * @param string $msg
     */
    public function send(string $msg)
    {
        if ($this->isActive()) {
            fputs($this->connection, $msg . "\r\n");
            $this->updateResponse();
        }
    }

    public function getReplyCode(): int
    {
        if (empty($this->response)) {
            return 0;
        }

        return (int) substr($this->response,0,3);
    }

    private function updateResponse()
    {
        $this->response = fgets($this->connection, self::RESPONSE_LENGTH);
    }

    /**
     * @return int
     */
    public function getErrNo(): int
    {
        return $this->errno ?? 0;
    }

    /**
     * @return string
     */
    public function getErrStr(): string
    {
        return $this->errstr ?? '';
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response ?? '';
    }

    /**
     * @return bool
     */
    public function hasErr(): bool
    {
        return !empty($this->errno);
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return !empty($this->connection);
    }
}
