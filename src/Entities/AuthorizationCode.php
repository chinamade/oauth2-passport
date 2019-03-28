<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/21
 * Time: 14:50
 */

namespace LianYun\Passport\Entities;

use Doctrine\ORM\Mapping as ORM;
/**
 * Class AuthorizationCode
 *
 * @package LianYun\Passport\Entities
 *
 * @ORM\Entity(repositoryClass="LianYun\Passport\Entities\Repositories\AuthorizationCodeRepository")
 * @ORM\Table(name="authorization_codes")
 *
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class AuthorizationCode
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint")
     */
    protected $id;
    /**
     * @var string
     * @ORM\Column(type="string", length=32, unique=true)
     */
    protected $code;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $user_id;
    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":"888"})
     */
    protected $app_id=888;
    /**
     * @var integer timestamp of expiration time
     * @ORM\Column(type="integer", name="expiration_time")
     */
    protected $expirationTime;
    
    public function __construct()
    {
        $this->code = md5(
            sprintf(
                "%s.%s.%s",
                getmypid(),
                (gethostname() ? : 'unknown-server'),
                microtime(true)
            )
        );
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }
    
    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }
    
    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }
    
    /**
     * @return int
     */
    public function getAppId(): int
    {
        return $this->app_id;
    }
    
    /**
     * @param int $app_id
     */
    public function setAppId(int $app_id): void
    {
        $this->app_id = $app_id;
    }
    
    /**
     * @return int
     */
    public function getExpirationTime(): int
    {
        return $this->expirationTime;
    }
    
    /**
     * @param int $expirationTime
     */
    public function setExpirationTime(int $expirationTime): void
    {
        $this->expirationTime = $expirationTime;
    }
    
 
}