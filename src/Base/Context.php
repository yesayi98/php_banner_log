<?php

namespace Base;

class Context
{
    private array $select = [];
    private array $where = [];
    private array $values = [];

    public function __construct(array $select = [], array $where = [], array $values = [])
    {
        $this->setSelect($select);
        $this->setWhere($where);
        $this->setValues($values);
    }

    /**
     * @param array $select
     */
    public function setSelect(array $select): void
    {
        $this->select = $select;
    }

    /**
     * @param array $values
     */
    public function setValues(array $values): void
    {
        $this->values = $values;
    }

    /**
     * @param array $where
     */
    public function setWhere(array $where): void
    {
        foreach ($where as $key => $value) {
            $this->where[] = [$key, '=', $value, 'AND'];
        }
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }
}