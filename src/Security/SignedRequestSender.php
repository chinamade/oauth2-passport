<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 15:13
 */

namespace LianYun\Passport\Security;

use Doctrine\ORM\EntityManager;
use LianYun\Passport\Entities\User;
use Symfony\Component\Security\Core\User\UserInterface;

class SignedRequestSender implements UserInterface
{
    protected $appId;
    protected $userId;
    /**
     * @var string[]
     */
    protected $roles;
    /**
     * @var EntityManager
     */
    protected $entityManager;
    public function __construct(EntityManager $entityManager, $appId=888, $userId=0, $roles=[])
    {
        $this->appId   = $appId;
        $this->userId  = $userId;
        $this->roles = $roles;
        $this->entityManager = $entityManager;
    }
    
    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        throw new \LogicException(__FUNCTION__ . " is not supported in " . static::class);
    }
    
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        throw new \LogicException(__FUNCTION__ . " is not supported in " . static::class);
    }
    
    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        throw new \LogicException(__FUNCTION__ . " is not supported in " . static::class);
    }
    
    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
    /**
     * @return int
     */
    public function getApp()
    {
        return $this->appId;
    }
    /**
     * @return null|User
     */
    public function getUser()
    {
        return $this->entityManager->find(User::class, $this->userId);
    }
}