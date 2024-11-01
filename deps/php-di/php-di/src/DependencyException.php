<?php

declare (strict_types=1);
namespace SmartemailingDeps\DI;

use SmartemailingDeps\Psr\Container\ContainerExceptionInterface;
/**
 * Exception for the Container.
 */
class DependencyException extends \Exception implements ContainerExceptionInterface
{
}
