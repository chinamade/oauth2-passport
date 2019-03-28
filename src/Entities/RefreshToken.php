<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/3/26
 * Time: 18:57
 */

namespace LianYun\Passport\Entities;

use Doctrine\ORM\Mapping as ORM;
use LianYun\Passport\Entities\Traits\IdTrait;
use Oasis\Mlib\Doctrine\AutoIdTrait;

/**
 * Class RefreshToken
 *
 * @package LianYun\Passport\Entities
 * @ORM\Entity()
 * @ORM\Table(name="refresh_tokens")
 */
class RefreshToken
{
    use IdTrait;
    /**
     * @var string
     * @ORM\Column(type="string", length=128, unique=true)
     */
    protected $code;
    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $user;
    /**
     * @var int
     * @ORM\Column(type="integer", options={"default":"888"})
     */
    protected $app_id = 888;
    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $expireAt;
    
    public function __construct($expireIn)
    {
        $this->expireAt = time() + $expireIn;
        $this->code     = md5(
            sprintf(
                "refresh_token.%s.%s.%s",
                getmypid(),
                (gethostname() ? : 'unknown-server'),
                microtime(true)
            )
        );
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
    public function getUser(): int
    {
        return $this->user;
    }
    
    /**
     * @param int $user
     */
    public function setUser(int $user): void
    {
        $this->user = $user;
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
    public function getExpireAt(): int
    {
        return $this->expireAt;
    }
    
    /**
     * @param int $expireAt
     */
    public function setExpireAt(int $expireAt): void
    {
        $this->expireAt = $expireAt;
    }
    
}
