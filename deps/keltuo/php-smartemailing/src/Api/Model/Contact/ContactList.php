<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Api\Model\Contact;

use SmartemailingDeps\JetBrains\PhpStorm\ArrayShape;
use SmartemailingDeps\JetBrains\PhpStorm\Pure;
use SmartemailingDeps\SmartEmailing\Api\Model\AbstractModel;
use SmartemailingDeps\SmartEmailing\Api\Model\ModelInterface;
use SmartemailingDeps\SmartEmailing\Exception\AllowedTypeException;
class ContactList extends AbstractModel implements ModelInterface
{
    public const CONFIRMED = 'confirmed';
    public const REMOVED = 'removed';
    public const UNSUBSCRIBED = 'unsubscribed';
    protected int $id;
    /**
     * Contact's status in Contactlist. Allowed values: "confirmed", "unsubscribed", "removed"
     */
    protected string $status = self::CONFIRMED;
    public function __construct(int $id, string $status)
    {
        $this->setId($id);
        $this->setStatus($status);
    }
    #[Pure]
    public function getIdentifier() : string
    {
        return (string) $this->getId();
    }
    public function setId(int $id) : ContactList
    {
        $this->id = \intval($id);
        return $this;
    }
    /**
     * Contact's status in Contact-list. Allowed values: "confirmed", "unsubscribed", "removed"
     */
    public function setStatus(string $status) : ContactList
    {
        AllowedTypeException::check($status, [self::CONFIRMED, self::UNSUBSCRIBED, self::REMOVED]);
        $this->status = $status;
        return $this;
    }
    public function getId() : int
    {
        return $this->id;
    }
    public function getStatus() : string
    {
        return $this->status;
    }
    #[ArrayShape(['id' => "int", 'status' => "string"])]
    public function toArray() : array
    {
        return ['id' => $this->getId(), 'status' => $this->getStatus()];
    }
}
