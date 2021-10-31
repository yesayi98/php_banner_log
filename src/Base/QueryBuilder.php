<?php

namespace Base;

use Exception;

class QueryBuilder
{
    protected string $query = '';
    protected string $table = '';
    protected string $method = 'select';
    protected array $wheres = [];
    protected array $select = ['*'];
    protected array $values = [];

    /**
     * @param string $method
     * @param string $table
     * @param ?Context $context
     */
    public function __construct(string $method = '', string $table = '', Context $context = null)
    {
        $this->method = $method;

        $this->table = $table;

        if ($context) {
            $this->applyContext($context);
        }
    }

    public function applyContext(Context $context)
    {
        $this->wheres = $context->getWhere();
        $this->select = $context->getSelect();
        $this->values = $context->getValues();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getQuery(): string
    {
        if (empty($this->query)) {
            $this->generate();
        }

        return $this->query;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @param array $wheres
     */
    protected function setWheres(array $wheres): void
    {
        $this->wheres[] = $wheres;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $assignValues = [];

        foreach ($values as $key => $value) {
            $assignValues[$key] = htmlspecialchars(trim($value));
        }

        $this->values[] = $assignValues;
    }

    /**
     * @param array $select
     */
    public function setSelect(array $select): void
    {
        $this->select = array_merge($this->select, $select);
    }

    /**
     * @param string $method
     * @return QueryBuilder
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @throws Exception
     */
    public function generate()
    {
        switch ($this->method) {
            case 'select':
                $query = 'SELECT ' . implode(', ', $this->select) . ' FROM ' . $this->table . ' ' . $this->applyWheres();
                break;
            case 'insert':
                $query = 'INSERT INTO ' . $this->table . ' (' . $this->applyColumns() . ')' . ' VALUES ' . '(' . $this->applyValues() . ')';
                break;
            case 'update':
                $query = 'UPDATE ' . $this->table . ' SET ' . $this->applyAssignedValues() . ' ' . $this->applyWheres();
                break;
            case 'delete':
                $query = 'DELETE FROM ' . $this->table . $this->applyWheres();
                break;
            default:
                throw new Exception("$this->method is not supported");
        }

        $this->query = $query;
    }

    public function select($selects = [], $table = ''): QueryBuilder
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        if (!empty($selects)) {
            $this->setSelect($selects);
        }
        return $this->setMethod(__METHOD__);
    }

    /**
     * @param string $table
     * @param array $values
     * @return $this
     */
    public function update(string $table = '', array $values = []): QueryBuilder
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        if (!empty($values)) {
            $this->setValues($values);
        }

        return $this->setMethod(__METHOD__);
    }

    /**
     * @param string $table
     * @param array $values
     * @return $this
     */
    public function insert(string $table = '', array $values = []): QueryBuilder
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        if (!empty($values)) {
            $this->setValues($values);
        }
        return $this->setMethod(__METHOD__);
    }

    public function delete(string $table): QueryBuilder
    {
        if (!empty($table)) {
            $this->setTable($table);
        }

        return $this->setMethod(__METHOD__);
    }

    public function where($column, $operator, $value, $and = true): QueryBuilder
    {
        if (is_array($column)) {
            foreach ($column as $item) {
                $this->where($item[0], $item[1], $item[2], $item[3] ?? true);
            }
        }

        $this->setWheres([$column, $operator, $value, $and]);

        return $this;
    }

    public static function __callStatic($method, $arguments)
    {
        return (new self())->{$method}(...$arguments);
    }

    private function applyWheres(): string
    {
        $queryResult = ' WHERE ';
        $query = '';
        foreach ($this->wheres as $key => $where) {
            $query .= $where[0] . ' ' . $where[1] . ' "' . $where[2] . '" ';

            if (isset($where[3]) && isset($this->wheres[$key+1])) {
                if ($where[3]) {
                    $query .= ' AND ';
                } else {
                    $query .= ' OR ';
                }
            }
        }

        return $queryResult . $query;
    }

    private function applyColumns(): string
    {
        return implode(', ', array_keys($this->values));
    }

    private function applyValues(): string
    {
        $query = '';

        foreach ($this->values as $value) {
            $query .= "'$value', ";
        }

        return substr($query, 0, strlen($query) - 2);
    }

    private function applyAssignedValues(): string
    {
        $query = '';

        foreach ($this->values as $key => $value) {
            $query .= "$key = '$value', ";
        }

        return substr($query, 0, strlen($query) - 2);
    }
}