<?php

declare (strict_types=1);
namespace SmartemailingDeps\Wpify\Model;

use WP_User;
use SmartemailingDeps\Wpify\Model\Attributes\Meta;
use SmartemailingDeps\Wpify\Model\Attributes\SourceObject;
use SmartemailingDeps\Wpify\Model\Exceptions\CouldNotSaveModelException;
use SmartemailingDeps\Wpify\Model\Exceptions\RepositoryNotInitialized;
use SmartemailingDeps\Wpify\Model\Interfaces\ModelInterface;
/**
 * Repository for the User model.
 * @method User create( array $data )
 */
class UserRepository extends Repository
{
    /**
     * Returns the model class name.
     * @return string
     */
    public function model() : string
    {
        return User::class;
    }
    /**
     * Returns the User model by the WP_User object, id, login, email or slug
     *
     * @param mixed $source
     *
     * @return ?User
     * @throws RepositoryNotInitialized
     */
    public function get(mixed $source) : ?ModelInterface
    {
        $wp_user = null;
        $user = null;
        $model = $this->model();
        if ($source instanceof $model) {
            return $source;
        }
        if ($source instanceof WP_User) {
            $wp_user = $source;
        }
        if (!$wp_user) {
            $wp_user = get_user_by('id', $source);
        }
        if (!$wp_user) {
            $wp_user = get_user_by('login', $source);
        }
        if (!$wp_user) {
            $wp_user = get_user_by('email', $source);
        }
        if (!$wp_user) {
            $wp_user = get_user_by('slug', $source);
        }
        if ($wp_user) {
            $model_class = $this->model();
            $user = new $model_class($this->manager());
            $user->source($wp_user);
        }
        return $user;
    }
    /**
     * Returns the current user.
     * @return ?User
     * @throws RepositoryNotInitialized
     */
    public function get_current() : ?User
    {
        $current_user = wp_get_current_user();
        if ($current_user) {
            return $this->get($current_user);
        }
        return null;
    }
    /**
     * Saves the user to the database.
     *
     * @param ModelInterface $model
     *
     * @return ModelInterface
     * @throws CouldNotSaveModelException
     */
    public function save(ModelInterface $model) : ModelInterface
    {
        $data = array();
        foreach ($model->props() as $prop) {
            if ($prop['readonly']) {
                continue;
            }
            $source = $prop['source'];
            if (\method_exists($model, 'persist_' . $prop['name'])) {
                $model->{'persist_' . $prop['name']}($model->{$prop['name']});
            } elseif ($source instanceof SourceObject) {
                $key = \preg_replace('/^data\\./', '', $source->key ?? $prop['name']);
                $data[$key] = $model->{$prop['name']};
            } elseif ($source instanceof Meta) {
                $key = $source->meta_key ?? $prop['name'];
                $data['meta_input'][$key] = $model->{$prop['name']};
            }
        }
        if ($data['ID'] > 0) {
            $result = wp_update_user($data);
            $action = 'update';
        } else {
            $result = wp_insert_user($data);
            $action = 'insert';
        }
        if (is_wp_error($result)) {
            throw new CouldNotSaveModelException($result->get_error_message());
        }
        if (apply_filters('wpify_model_refresh_model_after_save', \true, $model, $this)) {
            $model->refresh(get_user_by('id', $result));
        }
        do_action('wpify_model_repository_save_' . $action, $model, $this);
        return $model;
    }
    /**
     * Deletes the given user.
     *
     * @param User $model
     * @param bool $force_delete Unused.
     *
     * @return bool
     */
    public function delete(ModelInterface $model, bool $force_delete = \true) : bool
    {
        return wp_delete_user($model->id);
    }
    /**
     * Returns a collection of users.
     * @see https://developer.wordpress.org/reference/functions/get_users/
     *
     * @param array $args
     *
     * @return array
     * @throws RepositoryNotInitialized
     */
    public function find(array $args = array()) : array
    {
        $defaults = array();
        $args = wp_parse_args($args, $defaults);
        $collection = array();
        $users = get_users($args);
        foreach ($users as $user) {
            $collection[] = $this->get($user);
        }
        return $collection;
    }
    /**
     * Returns all users.
     *
     * @param array $args
     *
     * @return array
     * @throws RepositoryNotInitialized
     */
    public function find_all(array $args = array()) : array
    {
        return $this->find($args);
    }
    public function find_by_ids(array $ids) : array
    {
        return $this->find(array('include' => $ids));
    }
}
