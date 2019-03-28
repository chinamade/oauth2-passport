<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/1/15
 * Time: 09:45
 */

namespace LianYun\Passport\Entities\Repositories;

use LianYun\Passport\Entities\User;
use LianYun\Passport\Exceptions\DuplicateEntryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository extends BaseRepository
{
    const  LIMIT = 10;
    
    public function createRecord($username, $password, $ip = '')
    {
        if ($this->getUserByUsername($username)) {
            throw new DuplicateEntryException("The $username has exists.");
        }
        if ($this->getUserByEmail($username)) {
            throw new DuplicateEntryException("The $username has exists.");
        }
        
        $em   = $this->getEntityManager();
        $user = new User();
        $user->setUname($username);
        $user->setEmail($username);
        $user->setPwd($password);
        $user->setRegistTime(time());
        $user->setRegistIp($ip);
        
        $em->persist($user);
        $em->flush();
        
        return $user;
    }
    
    /**
     * @param $id
     *
     * @return null|object
     * @throws EntityNotFoundException
     */
    public function getUserById($id)
    {
        $user = $this->getEntityManager()->find(User::class, $id);
        if (!$user) {
            throw EntityNotFoundException::fromClassNameAndIdentifier(get_class($this), ["id" => $id]);
        }
        
        return $user;
    }
    
    /**
     * @param $username
     *
     * @return User|null
     */
    public function getUserByUsername($username)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(['user'])
           ->from(User::class, 'user')
           ->where('user.uname = :username')
           ->setParameter('username', $username);
        
        $result = $qb->getQuery()->getResult();
        if (count($result) == 0) {
            return null;
        }
        else {
            return array_shift($result);
        }
        
    }
    
    /**
     * @param $account
     *
     * @return User|null
     */
    public function getUserByUsernameOrEmail($account)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(['user'])
           ->from(User::class, 'user')
           ->where('user.uname = :account')
           ->orWhere('user.email = :account')
           ->setParameter('account', $account);
        
        $result = $qb->getQuery()->getResult();
        if (count($result) == 0) {
            return null;
        }
        else {
            return array_shift($result);
        }
        
    }
    
    /**
     * @param $email
     *
     * @return User|null
     */
    public function getUserByEmail($email)
    {
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(['user'])
           ->from(User::class, 'user')
           ->where('user.email = :email')
           ->setParameter('email', $email);
        
        $result = $qb->getQuery()->getResult();
        if (count($result) == 0) {
            return null;
        }
        else {
            return array_shift($result);
        }
        
    }
    
    /**
     * @param $id
     *
     * @return bool
     */
    public function remove($id)
    {
        $em   = $this->getEntityManager();
        $user = $this->getUserById($id);
        $em->remove($user);
        
        return true;
    }
    
    /**
     * @param $id
     * @param $old_password
     * @param $new_password
     *
     * @return null|object
     * @throws EntityNotFoundException
     */
    public function changePassword($id, $old_password, $new_password)
    {
        $em   = $this->getEntityManager();
        $user = $this->getUserById($id);
        if ($user->verifyPassowrd($old_password)) {
            $user->setPassword(trim($new_password));
            $em->persist($user);
            
            return $user;
        }
        else {
            throw EntityNotFoundException::fromClassNameAndIdentifier(get_class($this), ["id" => $id]);
        }
    }
    
    /**
     * @param $id
     *
     * @return string
     */
    public function getUserFullNameById($id)
    {
        $entityManager = $this->getEntityManager();
        /** @var User $User */
        $User = $entityManager->getRepository(User::class)
                              ->findOneBy(
                                  ['virtualUser' => $id]
                              );
        
        return $User->getFullname();
        
    }
    
    public function getUserForLoginCheck($username)
    {
        $em = $this->getEntityManager();
        
        if (!($user = $em->getRepository(User::class)->findOneBy(
                [
                    'uname' => $username,
                ]
            ))
            && !($user = $em->getRepository(User::class)->findOneBy(
                [
                    'email' => $username,
                ]
            ))
        ) {
            mdebug("Username: %s not exists ...", $username);
            throw new NotFoundHttpException(
                'Username or password is invalid.'
            );
        }
        
        return $user;
    }
    
}