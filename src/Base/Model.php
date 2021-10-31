<?php

namespace Base;

use Exception;
use ReflectionClass;
use ReflectionProperty;

abstract class Model
{
    protected ?int $id = null;
    private array $properties;
    /**
     * @var mixed
     */
    private Connection $connection;

    public function __construct($values = [])
    {
        $this->properties = (new ReflectionClass(get_called_class()))->getProperties(ReflectionProperty::IS_PUBLIC);
        $this->connection = App::get('db_connection');

        $this->assignValues($values);
    }

    private function assignValues(array $values)
    {
            foreach ($this->properties as $property) {
                if (!empty($values[$property->getName()])) {
                    $this->{$property->getName()} = $values[$property->getName()];
                }
            }

            if ($values['id']) {
                $this->id = $values['id'];
            }
    }

    public static function __callStatic($name, $arguments)
    {
        $instance = new static();

        return $instance->{$name}(...$arguments);
    }

    /**
     * @throws Exception
     */
    public function save(): self
    {
        if ($this->id) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * @param array $values
     * @param array $where
     * @return array|Model
     * @throws Exception
     */
    protected function update(array $values = [], array $where = [])
    {
        $this->assignValues($values);

        if (empty($where)) {
            $where['id'] = $this->getId();
        }

        $context = new Context();
        $context->setWhere($where);
        $context->setValues($this->getValues());

        return $this->runQuery('update', $context);
    }

    /**
     * @param array $values
     * @return Model
     * @throws Exception
     */
    protected function insert(array $values = []): self
    {
        $this->assignValues($values);

        $context = new Context();
        $context->setValues($this->getValues());

        $this->runQuery('insert', $context);

        return $this;
    }

    /**
     * @param Context $context
     * @return array|Model
     * @throws Exception
     */
    protected function get(Context $context)
    {
        return $this->runQuery('select', $context);
    }

    /**
     * @param int|null $id
     * @return array|Model
     * @throws Exception
     */
    protected function delete(int $id = null) {
        $where['id'] = $id ?? $this->getId();

        $context = new Context();
        $context->setWhere($where);

        return $this->runQuery('delete', $context);
    }

    /**
     * @return array
     */
    private function getValues(): array
    {
        $values = [];

        foreach ($this->properties as $property) {
            $values[$property->getName()] = $this->{$property->getName()};
        }

        return $values;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $type
     * @param Context $context
     * @return array|Model
     * @throws Exception
     */
    protected function runQuery(string $type, Context $context)
    {
        $query = (new QueryBuilder($type, $this->getTable(), $context))->getQuery();

        $result = $this->connection->execute($query);

        if ($result->isSuccess()){
            if (!$result->getData() && $this->getId()){
                return $this;
            }

            return $this->prepareData($result->getData());
        }

        throw new Exception($result->getErrorMessage());
    }

    private function prepareData(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $model = get_called_class();

        $result = [];

        foreach ($data as $datum) {
            $result[] = new $model((array) $datum);
        }

        return $result;
    }
}