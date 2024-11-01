<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Attributes;

use Attribute;
use SmartemailingDeps\Wpify\Model\Exceptions\RepositoryMethodNotImplementedException;
use SmartemailingDeps\Wpify\Model\Exceptions\RepositoryNotFoundException;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\SourceAttributeInterface;
use SmartemailingDeps\Wpify\Model\Post;
#[Attribute(Attribute::TARGET_PROPERTY)]
class ChildTermsRelation implements SourceAttributeInterface
{
    /**
     * Gets child terms of given model.
     *
     * @param ModelInterface $model
     * @param string $key
     *
     * @return Post
     * @throws RepositoryMethodNotImplementedException
     * @throws RepositoryNotFoundException
     */
    public function get(ModelInterface $model, string $key) : mixed
    {
        $manager = $model->manager();
        $repository = $manager->get_model_repository(\get_class($model));
        if (\method_exists($repository, 'find_child_terms_of')) {
            return $repository->find_child_terms_of($model);
        }
        throw new RepositoryMethodNotImplementedException('Repository method find_child_terms_of is not implemented in ' . \get_class($repository));
    }
}
