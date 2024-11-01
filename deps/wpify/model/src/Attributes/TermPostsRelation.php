<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Attributes;

use Attribute;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\SourceAttributeInterface;
#[Attribute(Attribute::TARGET_PROPERTY)]
class TermPostsRelation implements SourceAttributeInterface
{
    /**
     * @param class-string $target_entity
     */
    public function __construct(public string $target_entity)
    {
    }
    public function get(ModelInterface $model, string $key) : mixed
    {
        $manager = $model->manager();
        $repository = $manager->get_model_repository($this->target_entity);
        return $repository->find_all_by_term($model);
    }
}
