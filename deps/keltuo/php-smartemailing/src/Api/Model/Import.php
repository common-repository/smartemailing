<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model;

use SmartemailingDeps\JetBrains\PhpStorm\ArrayShape;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\ContactBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Contact\Settings;
class Import extends AbstractModel
{
    protected ContactBag $contactBag;
    protected ?Settings $settings;
    public function __construct(ContactBag $contactBag, ?Settings $settings = null)
    {
        $this->setContactBag($contactBag);
        $this->setSettings($settings);
    }
    public function getSettings() : ?Settings
    {
        return $this->settings;
    }
    public function setSettings(?Settings $settings) : Import
    {
        $this->settings = $settings;
        return $this;
    }
    public function getContactBag() : ContactBag
    {
        return $this->contactBag;
    }
    public function setContactBag(ContactBag $contactBag) : Import
    {
        $this->contactBag = $contactBag;
        return $this;
    }
    #[ArrayShape(['settings' => "SmartemailingDeps\\SmartEmailing\\Api\\Model\\Settings", 'data' => "SmartemailingDeps\\SmartEmailing\\Api\\Model\\ContactBag"])]
    public function toArray() : array
    {
        return \array_filter(['settings' => $this->getSettings(), 'data' => $this->getContactBag()], static fn($item) => !\is_null($item));
    }
}
