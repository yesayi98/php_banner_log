<?php

namespace Base;

use PDO;

class Connection
{
    private PDO $connection;
    private int $rowCount;
    private bool $success;
    /**
     * @var array|false
     */
    private $data;
    private string $errorCode;
    private array $errorMessage;

    /**
     * @param string $driver
     * @param string $host
     * @param int $port
     * @param string $db
     * @param string $username
     * @param string $password
     */
    public function __construct(string $driver, string $host, int $port, string $db, string $username, string $password)
    {
        try {
            $this->connection = new PDO("$driver:host=$host:$port;dbname=$db", $username, $password);
            // set the PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $exception) {
            throw new \PDOException($exception);
        }
    }

    public function execute(string $query): Connection
    {
        $statement = $this->connection->prepare($query);

        $this->rowCount = $statement->rowCount();

        $this->success = $statement->execute();
        $this->data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $this->errorMessage = $statement->errorInfo();
        $this->errorCode = $statement->errorCode();

        return $this;
    }

    /**
     * @return array|false
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getErrorMessage(): array
    {
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function __call($method, $arguments)
    {
        $this->connection->{$method}(...$arguments);
    }
}
