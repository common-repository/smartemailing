<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Search;

use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class CustomFieldOptions extends SingleCustomFieldOptions
{
    #[Pure]
    protected function getSortAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
    #[Pure]
    protected function getFilterAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
}
