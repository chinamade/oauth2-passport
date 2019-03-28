<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2018/1/29
 * Time: 17:59
 */

namespace LianYun\Passport\Entities\Traits;


use Doctrine\ORM\Mapping as ORM;

trait CreateTimeTrait
{
    /**
     * @var int
     * @ORM\Column(type="integer",name="create_time")
     */
    protected $createTime=0;
    
    /**
     * @return int
     */
    public function getCreateTime()
    {
        return $this->createTime;
    }
    
    /**
     * @param int $createTime
     */
    public function setCreateTime($createTime)
    {
        $this->createTime = $createTime;
    }
    
}