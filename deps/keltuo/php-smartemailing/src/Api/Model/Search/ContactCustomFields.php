<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Search;

use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class ContactCustomFields extends AbstractSearch
{
    public const ID = 'id';
    public const CONTACT_ID = 'contact_id';
    public const CUSTOM_FIELD_ID = 'customfield_id';
    public const VALUE = 'value';
    public const CUSTOM_FIELD_OPTIONS_ID = 'customfield_options_id';
    protected function getDefaultFields() : array
    {
        return [self::ID, self::CONTACT_ID, self::CUSTOM_FIELD_ID, self::VALUE, self::CUSTOM_FIELD_OPTIONS_ID];
    }
    protected function getSelectAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
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
