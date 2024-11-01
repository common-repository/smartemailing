<?php

/** @noinspection PhpMultipleClassDeclarationsInspection */
declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model\Attributes;

use Attribute;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\SourceAttributeInterface;
use SmartemailingDeps\Wpify\Model\PostRepository;
#[Attribute(Attribute::TARGET_PROPERTY)]
class PostTermsRelation implements SourceAttributeInterface
{
    /**
     * @param class-string $target_entity
     */
    public function __construct(public string $target_entity)
    {
    }
    public function get(ModelInterface $model, ?string $key = null) : mixed
    {
        $manager = $model->manager();
        $target_repository = $manager->get_model_repository($this->target_entity);
        return $target_repository->find_terms_of_post($model->id);
    }
    public function persist(ModelInterface $post, string $key, array $terms) : void
    {
        $manager = $post->manager();
        /** @var PostRepository $source_repository */
        $source_repository = $manager->get_model_repository($post::class);
        if (\method_exists($source_repository, 'assign_post_to_term')) {
            $source_repository->assign_post_to_term($post, $terms);
        }
    }
}
