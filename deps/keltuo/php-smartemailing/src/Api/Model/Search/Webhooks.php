<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Search;

use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class Webhooks extends AbstractSearch
{
    public const ID = 'id';
    public const URL = 'url';
    public const METHOD = 'method';
    public const EVENT = 'event';
    public const ACTIVE = 'active';
    public const TIMEOUT = 'timeout';
    public const LAST_RESPONSE_CODE = 'last_response_code';
    public const LAST_CALL_TIME = 'last_call_time';
    public const ERRORS_IN_ROW = 'errors_in_row';
    protected function getDefaultFields() : array
    {
        return [self::ID, self::URL, self::METHOD, self::EVENT, self::ACTIVE, self::TIMEOUT, self::LAST_RESPONSE_CODE, self::LAST_CALL_TIME, self::ERRORS_IN_ROW];
    }
    protected function getSelectAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
    #[Pure]
    protected function getFilterAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
}
