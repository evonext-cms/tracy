<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use Closure;
use EvoNext\Tracy\Contracts\IAjaxPanel;
use Illuminate\Support\Arr;

class AuthPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * The user resolver callable.
     *
     * @var callable|null
     */
    protected $userResolver;

    /**
     * setUserResolver.
     *
     * @param Closure $userResolver
     * @return $this
     */
    public function setUserResolver(Closure $userResolver)
    {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * getAttributes.
     ** @return array
     */
    protected function getAttributes(): array
    {
        $attributes = [];
        if (is_null($this->userResolver) === false) {
            $attributes['rows'] = call_user_func($this->userResolver);
        } elseif ($this->hasLaravel() === true) {
            $attributes = isset($this->laravel['sentinel']) === true ?
                $this->fromSentinel() : $this->fromGuard();
        }

        return $this->identifier($attributes);
    }

    /**
     * fromGuard.
     *
     * @return array
     */
    protected function fromGuard()
    {
        $user = $this->user();

        return is_null($user) === true
            ? []
            : [
                'id'   => $user->getAuthIdentifier(),
                'rows' => $user->toArray(),
            ];
    }

    /**
     * fromSentinel.
     *
     * @return array
     */
    protected function fromSentinel()
    {
        $user = $this->laravel['sentinel']->check();

        return empty($user) === true
            ? []
            : [
                'id'   => null,
                'rows' => $user->toArray(),
            ];
    }

    /**
     * identifier.
     *
     * @param array $attributes
     * @return array
     */
    protected function identifier(array $attributes = []): array
    {
        $id      = Arr::get($attributes, 'id');
        $rows    = Arr::get($attributes, 'rows', []);
        $roles   = Arr::get($attributes, 'roles', []);
        $perms   = Arr::get($attributes, 'perms', []);
        $isAdmin = Arr::get($attributes, 'isAdmin', false);

        if (empty($rows) === true) {
            $id = 'Guest';
        } elseif (is_numeric($id) === true || empty($id) === true) {
            $id = 'UnKnown';
            foreach (['name', 'email', 'id'] as $key) {
                if (isset($rows[$key]) === true) {
                    $id = $rows[$key];
                    break;
                }
            }
        }

        if ($this->user()) {
            $roles = $this->user()
                ->roles()
                ->get()
                ->mapWithKeys(fn($item) => [$item['name'] => $item['description']])
                ->toArray();

            $perms = $this->user()
                ->allPermissions()
                ->mapWithKeys(fn($item) => [$item['slug'] => $item['name']])
                ->toArray();

            $isAdmin = $this->user()->hasRole(1);
        }


        return [
            'id'      => $id,
            'rows'    => $rows,
            'roles'   => $roles,
            'perms'   => $perms,
            'isAdmin' => $isAdmin,
        ];
    }

    /**
     * @return \Core\Models\User
     */
    protected function user()
    {
        return $this->laravel['auth']->user();
    }
}
