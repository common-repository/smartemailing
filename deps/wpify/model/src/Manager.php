<?php

declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model;

use SmartemailingDeps\Wpify\Model\Exceptions\RepositoryNotFoundException;
use SmartemailingDeps\Wpify\Model\Factories\DefaultStorageFactory;
use SmartemailingDeps\Wpify\Model\Interfaces\RepositoryInterface;
use SmartemailingDeps\Wpify\Model\Interfaces\StorageFactoryInterface;
/**
 * Manager for the repositories.
 */
class Manager
{
    /**
     * Registered model to repository relations.
     * The key is the model class name, the value is its repository.
     * @var RepositoryInterface[]
     */
    private array $model_repository_relations = array();
    /**
     * Registered repositories.
     * The key is the model class name, the value is the repository.
     * @var RepositoryInterface[]
     */
    private array $repositories = array();
    /**
     * Constructor for the Manager.
     * It accepts dependencies with the following interfaces:
     * - RepositoryInterface: Custom repository for models.
     * All other dependencies are ignored.
     * You can also register custom repositories after the manager is created by using the register_repository method.
     *
     * @param  array  $dependencies  User defined repositories.
     */
    public function __construct(...$dependencies)
    {
        $default_repositories = array(AttachmentRepository::class, CategoryRepository::class, CommentRepository::class, MenuItemRepository::class, MenuRepository::class, OrderRepository::class, OrderItemRepository::class, OrderItemLineRepository::class, OrderItemShippingRepository::class, OrderItemFeeRepository::class, PageRepository::class, PostRepository::class, PostTagRepository::class, ProductRepository::class, ProductCatRepository::class, SiteRepository::class, TermRepository::class, UserRepository::class);
        // Resolve repositories.
        foreach ($default_repositories as $repository) {
            $this->register_repository(new $repository());
        }
        foreach ($dependencies as $dependency) {
            if ($dependency instanceof RepositoryInterface) {
                $this->register_repository($dependency);
            }
        }
    }
    /**
     * Registers the repository for models.
     *
     * @param  RepositoryInterface  $repository
     *
     * @return void
     */
    public function register_repository(RepositoryInterface $repository) : void
    {
        $repository->manager($this);
        $this->repositories[\get_class($repository)] = $repository;
        $this->model_repository_relations[$repository->model()] = $repository;
    }
    /**
     * Returns the repository instance for the given model.
     *
     * @param  string  $model
     *
     * @return RepositoryInterface
     * @throws RepositoryNotFoundException
     */
    public function get_model_repository(string $model) : RepositoryInterface
    {
        if (!isset($this->model_repository_relations[$model])) {
            throw new RepositoryNotFoundException('Repository for model `' . $model . '` not found.');
        }
        return $this->model_repository_relations[$model];
    }
    /**
     * Returns given repository instance.
     * @template T
     *
     * @param  string|class-string<T>  $repository_class  Repository class name.
     *
     * @return mixed|T
     */
    public function get_repository(string $repository_class)
    {
        if (!isset($this->repositories[$repository_class])) {
            $this->repositories[$repository_class] = new $repository_class($this);
        }
        return $this->repositories[$repository_class];
    }
    /**
     * Get repositories.
     * @return array
     */
    public function get_repositories() : array
    {
        return $this->repositories;
    }
}
