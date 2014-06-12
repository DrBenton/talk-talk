<?php

namespace TalkTalk\CorePlugin\Auth\Service;

use TalkTalk\Core\Service\BaseService;
use TalkTalk\Core\ApplicationInterface;
use TalkTalk\Model\User as UserModel;

class User extends BaseService
{

    protected $isAuthenticated;
    protected $isAnonymous;
    protected $user;

    public function setApplication(ApplicationInterface $app)
    {
        parent::setApplication($app);

        $this->isAuthenticated = $this->app->get('session')->has('userId');
        $this->isAnonymous = ! $this->isAuthenticated;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    /**
     * @return bool
     */
    public function isAnonymous()
    {
        return $this->isAnonymous;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static
     * @throws \DomainException
     */
    public function getUser()
    {
        if (null !== $this->user) {
            return $this->user;
        }

        if ($this->isAnonymous) {
            throw new \DomainException('No authenticated User found!');
        }

        return UserModel::findOrFail($this->app->get('session')->get('userId'));
    }

}
