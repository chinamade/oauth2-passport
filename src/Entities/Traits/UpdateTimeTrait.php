<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2019/1/11
 * Time: 14:53
 */

namespace LianYun\Passport\Entities\Traits;

trait UpdateTimeTrait
{
    /**
     * @var  int
     * @ORM\Column(type="integer",name="update_time", options={"default":0})
     */
    protected $updateTime = 0;
    
    /**
     * @return int
     */
    public function getUpdateTime()
    {
        return $this->updateTime;
    }
    
    /**
     * @param int $updateTime
     */
    public function setUpdateTime($updateTime)
    {
        $this->updateTime = $updateTime;
    }
}