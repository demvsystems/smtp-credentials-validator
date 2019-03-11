<?php

namespace Demv\SmtpCredentialsValidator;

class Connector
{
    const RESPONSE_LENGTH = 4096;
    const CRYPTO_METHOD   = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;

    /**
     * @var resource
     */
    private $socket;
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
     * @return int 0 if no reply code
     */
    public function getReplyCode(): int
    {
        return (int) substr($this->response, 0, 3) ?? 0;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return isset(socket_get_status($this->socket)['crypto']);
    }

    /**
     * @return bool
     */
    public function open(): bool
    {
        $this->socket = stream_socket_client(
            sprintf('tcp://%s:%s', $this->host, $this->port),
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT
        );

        if ($this->socket === false) {
            return false;
        }

        $this->read();

        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        $this->send('QUIT');

        return $this->getReplyCode() === 221;
    }

    /**
     * @return bool
     */
    public function startTls(): bool
    {
        $this->send('STARTTLS');
        if ($this->getReplyCode() !== 220) {
            return false;
        }
        stream_socket_enable_crypto($this->socket, true, self::CRYPTO_METHOD);

        return $this->isEncrypted();
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
     * @return int
     */
    private function write(string $msg): int
    {
        $msg .= "\r\n";

        return fputs($this->socket, $msg, self::RESPONSE_LENGTH);
    }

    private function read()
    {
        $this->response = fread($this->socket, self::RESPONSE_LENGTH);
    }
}
