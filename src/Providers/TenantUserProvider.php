<?php

namespace Soluto\MultiTenant\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Str;
use Soluto\MultiTenant\Database\TenantScope;

class TenantUserProvider extends EloquentUserProvider
{
    /**
     * The name of the "password" attribute.
     *
     * @var string
     */
    protected $password;

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
    public function __construct(HasherContract $hasher, $model, $scope = null, $password = 'password')
    {
        parent::__construct($hasher, $model);
        $this->password = $password;
        $this->scope = $scope;
    }

    /**
     * @inheritdoc
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        $plain = $credentials[$this->getPassword()];

        return $this->hasher->check($plain, $user->getAuthPassword());
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
        if (empty($credentials)) {
            return;
        }

        $model = $this->createModel();
        $query = $this->prepareQuery($model->newQuery());

        foreach ($credentials as $key => $value) {
            if (! Str::contains($key, $this->getPassword())) {
                $query->where($key, $value);
            }
        }

        return $query->first();
    }

    /**
     * Get the name of the "password" attribute.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
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