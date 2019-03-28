<?php
/**
 * Created by PhpStorm.
 * User: qiudaoyu
 * Date: 2019/1/12
 * Time: 10:30 AM
 */

namespace LianYun\Passport\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index as Index;
use Doctrine\ORM\Mapping\UniqueConstraint as Unique;
use LianYun\Passport\Entities\Traits\UidTrait;
use Amopi\Mlib\Doctrine\CascadeRemovableInterface;
use Amopi\Mlib\Doctrine\CascadeRemoveTrait;
use LianYun\Passport\Entities\Traits\CreateTimeTrait;
use LianYun\Passport\Entities\Traits\GameAppidTrait;
use LianYun\Passport\Entities\Traits\IdTrait;

/**
 * Class User
 *
 * @package LianYun\Passport\Entities
 * @ORM\Entity(repositoryClass="LianYun\Passport\Entities\Repositories\UserRepository")
 * @ORM\Table(
 *     name="user",
 *     indexes={
 *          @Index(name="regist_time",columns={"regist_time"}),
 *          @Index(name="email_pwd",columns={"email","pwd"})
 *     },
 *     uniqueConstraints={
 *          @Unique(name="email",columns={"email"})
 *     }
 * )
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements \JsonSerializable, CascadeRemovableInterface
{
    use CascadeRemoveTrait;
    use IdTrait;
    use CreateTimeTrait;
    
    /**
     * @var string
     * @orm\column(type="string",length=100,options={"default":""})
     */
    protected $uname = '';
    /**
     * @var int
     * @orm\column(type="smallint",options={"default":"0"})
     */
    protected $sex = 0;
    /**
     * @var string
     * @orm\column(type="string",nullable=true)
     */
    protected $birth = '';
    /**
     * @var string
     * @orm\column(type="string",length=20,nullable=true,options={"default":""})
     */
    protected $mobilephone = '';
    /**
     * @var string
     * @orm\column(type="string",length=80,nullable=true,options={"default":""})
     */
    protected $nickname = '';
    /**
     * @var string
     * @orm\column(type="string",length=40,)
     */
    protected $email = '';
    /**
     * @var string
     * @orm\column(type="string",length=10,nullable=true,options={"default":""})
     */
    protected $lang = 'en-us';
    /**
     * @var int
     * @orm\column(type="string",length=20,nullable=true,options={"default":""})
     */
    protected $userfrom = '';
    /**
     * @var string
     * @orm\column(type="string",nullable=true,options={"default":""})
     */
    protected $pic = '';
    /**
     * @var string
     * @orm\column(type="string",length=80,nullable=true,options={"default":""})
     */
    protected $realname = '';
    /**
     * @var int
     * @orm\column(type="string",length=80,nullable=true,options={"default":""})
     */
    protected $email_active = 0;
    /**
     * @var string
     * @orm\column(type="string",length=40,nullable=true)
     */
    protected $safe_email = '';
    /**
     * @var string
     * @orm\column(type="string",length=80,nullable=true,options={"default":""})
     */
    protected $locale = '';
    /**
     * @var int
     * @orm\column(type="integer",length=80,nullable=true,options={"default":0})
     */
    protected $regist_time = 0;
    /**
     * @var string
     * @orm\column(type="string",length=64,nullable=true)
     */
    protected $regist_ip = '';
    /**
     * @var string
     * @orm\column(type="string",length=60)
     */
    protected $pwd;
    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $pwd_history = [];
    
    /**/
    public function __construct()
    {
    }
    
    /**
     * Send back customized information when calling json_encode method on UserInfo entity object
     *
     * @return array
     */
    function jsonSerialize()
    {
        
        return [
            'id'           => $this->getId(),
            'uname'        => $this->getUname(),
            'sex'          => $this->getSex(),
            'birth'        => $this->getBirth(),
            'mobilephone'  => $this->getMobilephone(),
            'nickname'     => $this->getNickname(),
            'email'        => $this->getEmail(),
            'lang'         => $this->getLang(),
            'pic'          => $this->getPic(),
            'realname'     => $this->getRealname(),
            'regist_time'  => $this->getRegistTime(),
        ];
    }
    
    /**
     * @return array an array of entities which will also be removed when the calling entity is remvoed
     */
    public function getCascadeRemoveableEntities()
    {
        $cascadeRemove = [];
        
        return $cascadeRemove;
    }
    
    public function getDirtyEntitiesOnInvalidation()
    {
        return [];
    }
    
    /**
     * @return string
     */
    public function getUname(): string
    {
        return $this->uname;
    }
    
    /**
     * @param string $uname
     */
    public function setUname(string $uname): void
    {
        $this->uname = $uname;
    }
    
    /**
     * @return int
     */
    public function getSex(): int
    {
        return $this->sex;
    }
    
    /**
     * @param int $sex
     */
    public function setSex(int $sex): void
    {
        $this->sex = $sex;
    }
    
    /**
     * @return string
     */
    public function getBirth(): string
    {
        return $this->birth;
    }
    
    /**
     * @param string $birth
     */
    public function setBirth(string $birth): void
    {
        $this->birth = $birth;
    }
    
    /**
     * @return string
     */
    public function getMobilephone(): string
    {
        return $this->mobilephone;
    }
    
    /**
     * @param string $mobilephone
     */
    public function setMobilephone(string $mobilephone): void
    {
        $this->mobilephone = $mobilephone;
    }
    
    /**
     * @return string
     */
    public function getNickname(): string
    {
        return $this->nickname;
    }
    
    /**
     * @param string $nickname
     */
    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getLang(): string
    {
        return $this->lang;
    }
    
    /**
     * @param string $lang
     */
    public function setLang(string $lang): void
    {
        $this->lang = $lang;
    }
    
    /**
     * @return int
     */
    public function getUserfrom(): int
    {
        return $this->userfrom;
    }
    
    /**
     * @param int $userfrom
     */
    public function setUserfrom(int $userfrom): void
    {
        $this->userfrom = $userfrom;
    }
    
    /**
     * @return string
     */
    public function getPic(): string
    {
        return $this->pic;
    }
    
    /**
     * @param string $pic
     */
    public function setPic(string $pic): void
    {
        $this->pic = $pic;
    }
    
    /**
     * @return string
     */
    public function getRealname(): string
    {
        return $this->realname;
    }
    
    /**
     * @param string $realname
     */
    public function setRealname(string $realname): void
    {
        $this->realname = $realname;
    }
    
    /**
     * @return int
     */
    public function getEmailActive(): int
    {
        return $this->email_active;
    }
    
    /**
     * @param int $email_active
     */
    public function setEmailActive(int $email_active): void
    {
        $this->email_active = $email_active;
    }
    
    /**
     * @return string
     */
    public function getSafeEmail(): string
    {
        return $this->safe_email;
    }
    
    /**
     * @param string $safe_email
     */
    public function setSafeEmail(string $safe_email): void
    {
        $this->safe_email = $safe_email;
    }
    
    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
    
    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }
    
    /**
     * @return int
     */
    public function getRegistTime(): int
    {
        return $this->regist_time;
    }
    
    /**
     * @param int $regist_time
     */
    public function setRegistTime(int $regist_time): void
    {
        $this->regist_time = $regist_time;
    }
    
    /**
     * @return string
     */
    public function getRegistIp(): string
    {
        return $this->regist_ip;
    }
    
    /**
     * @param string $regist_ip
     */
    public function setRegistIp(string $regist_ip): void
    {
        $this->regist_ip = $regist_ip;
    }
    
    /**
     * @return string
     */
    public function getPwd(): string
    {
        return $this->pwd;
    }
    
    /**
     * @param string $pwd
     */
    public function setPwd(string $pwd): void
    {
        $this->pwd = password_hash($pwd, PASSWORD_BCRYPT);
    }
    
    /**
     * @param $password
     *
     * @return boolean true if password matches, false otherwise
     */
    public function verifyPassowrd($password)
    {
        return password_verify($password, $this->getPwd());
    }
    
    /**
     * @return array
     */
    public function getPwdHistory(): array
    {
        return $this->pwd_history;
    }
    
    /**
     * @param array $pwd_history
     */
    public function setPwdHistory(array $pwd_history): void
    {
        $this->pwd_history = $pwd_history;
    }
    
}