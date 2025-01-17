<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Search;

use SmartemailingDeps\JetBrains\PhpStorm\Pure;
class CampaignStats extends AbstractSearch
{
    public const CAMPAIGN_NEWSLETTER = 'newsletter';
    public const CAMPAIGN_AUTORESPONDER = 'autoresponder';
    public const CAMPAIGN_CUSTOM_EMAIL = 'custom_email';
    public const CAMPAIGN_TRIGGER_ACTION = 'trigger_action';
    public const CAMPAIGN_TRANSACTIONAL_EMAIL = 'transactional_email';
    public const ID = 'id';
    public const CAMPAIGN_ID = 'campaign_id';
    public const CONTACT_ID = 'contact_id';
    public const OPENED = 'opened';
    public const CLICKED = 'clicked';
    public const UNSUBSCRIBE = 'unsubscribed';
    public const BOUNCED = 'bounced';
    public const TIME = 'time';
    public const EMAIL_ADDRESS = 'emailaddress';
    protected function getDefaultFields() : array
    {
        return [self::ID, self::CAMPAIGN_ID, self::CONTACT_ID, self::OPENED, self::CLICKED, self::UNSUBSCRIBE, self::BOUNCED, self::TIME, self::EMAIL_ADDRESS];
    }
    protected function getSelectAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
    protected function getActionTypeAllowedValues() : array
    {
        return [self::CAMPAIGN_NEWSLETTER, self::CAMPAIGN_AUTORESPONDER, self::CAMPAIGN_CUSTOM_EMAIL, self::CAMPAIGN_TRIGGER_ACTION, self::CAMPAIGN_TRANSACTIONAL_EMAIL];
    }
    #[Pure]
    protected function getFilterAllowedValues() : array
    {
        return $this->getDefaultFields();
    }
}
