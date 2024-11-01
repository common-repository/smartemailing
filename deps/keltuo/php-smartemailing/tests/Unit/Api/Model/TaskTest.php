<?php

namespace SmartemailingDeps\SmartEmailing\Test\Unit\Api\Model;

use SmartemailingDeps\PHPUnit\Framework\TestCase;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\AttachmentBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Bag\ReplaceBag;
use SmartemailingDeps\SmartEmailing\Api\Model\Recipient;
use SmartemailingDeps\SmartEmailing\Api\Model\Task;
class TaskTest extends TestCase
{
    public function testToArray()
    {
        $class = new Task(new Recipient('email@address.cz'));
        $this->assertEquals(['recipient' => new Recipient('email@address.cz'), 'replace' => new ReplaceBag(), 'attachments' => new AttachmentBag()], $class->toArray());
    }
}
