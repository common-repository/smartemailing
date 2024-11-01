<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Search;

use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class Emails extends SingleEmail
{
    #[Pure]
    protected function getSortAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
}
