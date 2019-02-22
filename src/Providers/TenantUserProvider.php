<?php

namespace Soluto\MultiTenant\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Str;
use Soluto\MultiTenant\Database\TenantScope;
use Illuminate\Contracts\Support\Arrayable;

class TenantUserProvider extends EloquentUserProvider
{
    /**
     * The name of the scope.
     *
     * @var string
     */
    protected $scope;

    /**
     * Create a new database user provider.
     *
     * @param \Illuminate\Contracts\Hashing\Hasher  $hasher
     * @param string $model
     * @param string $password
     * @param string|null $scope
     * @return void
     */
    public function __construct(HasherContract $hasher, $model, $scope = null)
    {
        parent::__construct($hasher, $model);
        $this->scope = $scope;
    }

    /**
     * @inheritdoc
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();
        $query = $model->newQuery();

        return $this->prepareQuery($query)->find($identifier);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();
        $query = $model->newQuery();

        return $this->prepareQuery($query)
            ->where($model->getAuthIdentifierName(), $identifier)
            ->where($model->getRememberTokenName(), $token)
            ->first();
    }

    /**
     * @inheritdoc
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
            (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        $model = $this->createModel();
        $query = $this->prepareQuery($model->newQuery());

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            if (is_array($value) || $value instanceof Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Get the name of the scope.
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Prepare query for find user
     *
     * @param $query \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function prepareQuery($query)
    {
        $builder = $query->withoutGlobalScope(TenantScope::class);

        $scope = $this->getScope();

        return ($scope) ? call_user_func([$builder, $scope]) : $builder;
    }

}