<?php

class Connector
{
    const RESPONSE_LENGTH = 4096;

    /**
     * @var resource
     */
    private $socket;
    /**
     * @var string
     */
    private $address;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $response;
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
        $this->address = gethostbyname($host);
        $this->port    = $port;
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false) {
            $this->errstr = 'socket_create() fehlgeschlagen. Grund: ' . socket_strerror(socket_last_error()) . PHP_EOL;

            return false;
        }

        $result = socket_connect($this->socket, $this->address, $this->port);
        if ($result === false) {
            $this->errstr = 'socket_connect() fehlgeschlagen. Grund: ' . socket_strerror(socket_last_error()) . PHP_EOL;

            return false;
        }

        $this->read();

        return true;
    }

    /**
     * @param string $msg
     */
    public function send(string $msg)
    {
        $this->write($msg);
        $this->read();
    }

    /**
     * @param string $msg
     *
     * @return bool
     */
    private function write(string $msg): bool
    {
        $msg    .= "\r\n";
        $length = strlen($msg);
        $sent   = 0;
        do {
            $msg    = substr($msg, $sent);
            $length -= $sent;

            $sent = socket_write($this->socket, $msg, $length);

            if ($sent === false) {
                return false;
            }
        } while ($sent < $length);

        return true;
    }

    /**
     * @return int
     */
    public function getReplyCode(): int
    {
        return (int) substr($this->response, 0, 3) ?? 0;
    }

    private function read()
    {
        $this->response = socket_read($this->socket, self::RESPONSE_LENGTH);
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
        return !empty($this->errstr);
    }

    /**
     * @return string
     */
    public function getErrstr(): string
    {
        return $this->errstr ?? '';
    }
}
