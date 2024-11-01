<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Test\Mock;

use SmartemailingDeps\JetBrains\PhpStorm\ArrayShape;
use SmartemailingDeps\JetBrains\PhpStorm\Pure;
use SmartemailingDeps\SmartEmailing\Api\Model\AbstractModel;
use SmartemailingDeps\SmartEmailing\Api\Model\ModelInterface;
class AbstractModelMock extends AbstractModel implements ModelInterface
{
    protected string $name;
    protected string $value;
    protected ?string $snakeCaseCamelCase = null;
    #[Pure]
    public function getIdentifier() : string
    {
        return $this->getName();
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function setName(string $name) : AbstractModelMock
    {
        $this->name = $name;
        return $this;
    }
    public function getValue() : string
    {
        return $this->value;
    }
    public function setValue(string $value) : AbstractModelMock
    {
        $this->value = $value;
        return $this;
    }
    public function getSnakeCaseCamelCase() : ?string
    {
        return $this->snakeCaseCamelCase;
    }
    public function setSnakeCaseCamelCase(string $snakeCaseCamelCase) : AbstractModelMock
    {
        $this->snakeCaseCamelCase = $snakeCaseCamelCase;
        return $this;
    }
    #[ArrayShape(['name_sm' => "string", 'value_sm' => "string", 'snake_sm' => "string"])]
    public function toArray() : array
    {
        return ['name_sm' => $this->getName(), 'value_sm' => $this->getValue(), 'snake_sm' => $this->getSnakeCaseCamelCase()];
    }
}
