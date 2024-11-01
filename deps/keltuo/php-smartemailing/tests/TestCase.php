<?php

declare (strict_types=1);
namespace SmartemailingDeps\SmartEmailing\Test;

use SmartemailingDeps\GuzzleHttp\Client;
use SmartemailingDeps\PHPUnit\Framework\MockObject\MockObject;
use SmartemailingDeps\PHPUnit\Framework\TestCase as BaseTestCase;
use SmartemailingDeps\Psr\Http\Message\ResponseInterface;
use SmartemailingDeps\SmartEmailing\SmartEmailing;
use function array_merge;
abstract class TestCase extends BaseTestCase
{
    protected abstract function getApiClass() : string;
    protected mixed $defaultReturnResponse;
    protected function setUp() : void
    {
        parent::setUp();
        $this->defaultReturnResponse = '{
               "status": "ok",
               "meta": [
               ],
               "message": "Hi there! API version 3 here!"
           }';
    }
    /**
     * @param array $methods
     *
     * @return MockObject
     */
    protected function getApiMock(array $methods = []) : MockObject
    {
        $client = $this->createMock(Client::class);
        $api = $this->getMockBuilder(SmartEmailing::class)->onlyMethods(['getClient'])->setConstructorArgs(['username', 'api-key'])->getMock();
        $api->expects($this->any())->method('getClient')->willReturn($client);
        return $this->getMockBuilder($this->getApiClass())->onlyMethods(array_merge(['get', 'post', 'patch', 'delete', 'put'], $methods))->setConstructorArgs([$api])->getMock();
    }
}
