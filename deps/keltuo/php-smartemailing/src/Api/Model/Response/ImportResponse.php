<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Response;

use SmartemailingDeps\Psr\Http\Message\ResponseInterface;
class ImportResponse extends BaseResponse
{
    protected array $contacts_map = [];
    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);
        if (\property_exists($this->json, 'status')) {
            $this->setPropertyValue('contacts_map');
        }
        if (\is_array($this->contacts_map) && \count($this->contacts_map) > 0) {
            $this->data = $this->contacts_map;
        }
    }
    public function getContactsMap() : array
    {
        return $this->contacts_map;
    }
    public function toArray() : array
    {
        return \array_merge(parent::toArray(), ['contacts_map' => $this->getContactsMap()]);
    }
}
