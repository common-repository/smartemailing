<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing;

use SmartemailingDeps\GuzzleHttp\Client;
use SmartemailingDeps\JetBrains\PhpStorm\Pure;
use SmartemailingDeps\SmartEmailing\Api\Automation;
use SmartemailingDeps\SmartEmailing\Api\ContactLists;
use SmartemailingDeps\SmartEmailing\Api\Contacts;
use SmartemailingDeps\SmartEmailing\Api\CustomCampaigns;
use SmartemailingDeps\SmartEmailing\Api\CustomFieldOptions;
use SmartemailingDeps\SmartEmailing\Api\CustomFields;
use SmartemailingDeps\SmartEmailing\Api\Emails;
use SmartemailingDeps\SmartEmailing\Api\Eshops;
use SmartemailingDeps\SmartEmailing\Api\Import;
use SmartemailingDeps\SmartEmailing\Api\Newsletter;
use SmartemailingDeps\SmartEmailing\Api\ProcessingPurposes;
use SmartemailingDeps\SmartEmailing\Api\Scoring;
use SmartemailingDeps\SmartEmailing\Api\Stats;
use SmartemailingDeps\SmartEmailing\Api\Tests;
use SmartemailingDeps\SmartEmailing\Api\TransactionalEmails;
use SmartemailingDeps\SmartEmailing\Api\WebForms;
use SmartemailingDeps\SmartEmailing\Api\Webhooks;
class SmartEmailing
{
    private const BASE_URL = 'https://app.smartemailing.cz';
    private const USER_AGENT = 'sm-php-api-client/1.0.0';
    private const DOCUMENT_TYPE = 'application/json';
    protected string $baseUrl = self::BASE_URL;
    protected Client $client;
    protected static SmartEmailing $instance;
    public function __construct(string $username, string $apiKey, ?string $baseUrl = null)
    {
        $this->client = new Client(['auth' => [$username, $apiKey], 'base_uri' => $baseUrl ?? $this->baseUrl, 'headers' => ['Accept' => self::DOCUMENT_TYPE, 'User-Agent' => self::USER_AGENT]]);
    }
    #[Pure]
    public function automation() : Automation
    {
        return new Automation($this);
    }
    #[Pure]
    public function contactLists() : ContactLists
    {
        return new ContactLists($this);
    }
    #[Pure]
    public function contacts() : Contacts
    {
        return new Contacts($this);
    }
    #[Pure]
    public function customCampaigns() : CustomCampaigns
    {
        return new CustomCampaigns($this);
    }
    #[Pure]
    public function customFieldOptions() : CustomFieldOptions
    {
        return new CustomFieldOptions($this);
    }
    #[Pure]
    public function customFields() : CustomFields
    {
        return new CustomFields($this);
    }
    #[Pure]
    public function emails() : Emails
    {
        return new Emails($this);
    }
    #[Pure]
    public function eshops() : Eshops
    {
        return new Eshops($this);
    }
    #[Pure]
    public function import() : Import
    {
        return new Import($this);
    }
    #[Pure]
    public function newsletter() : Newsletter
    {
        return new Newsletter($this);
    }
    #[Pure]
    public function processingPurposes() : ProcessingPurposes
    {
        return new ProcessingPurposes($this);
    }
    #[Pure]
    public function scoring() : Scoring
    {
        return new Scoring($this);
    }
    #[Pure]
    public function stats() : Stats
    {
        return new Stats($this);
    }
    #[Pure]
    public function tests() : Tests
    {
        return new Tests($this);
    }
    #[Pure]
    public function transactionalEmails() : TransactionalEmails
    {
        return new TransactionalEmails($this);
    }
    #[Pure]
    public function webForms() : WebForms
    {
        return new WebForms($this);
    }
    #[Pure]
    public function webhooks() : Webhooks
    {
        return new Webhooks($this);
    }
    public function getClient() : Client
    {
        return $this->client;
    }
    protected function getBaseUrl() : string
    {
        return $this->baseUrl;
    }
}
