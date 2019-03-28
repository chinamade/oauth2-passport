<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2018/1/30
 * Time: 10:51
 */

namespace LianYun\Passport\Entities\Traits;

use Doctrine\ORM\Mapping as ORM;

trait DelTrait
{
    /**
     * @var int
     * @ORM\Column(type="integer",name="is_del",options={"default":0})
     */
    protected $isDel = 0;
    
    /**
     * @return int
     */
    public function getIsDel()
    {
        return $this->isDel ? : 0;
    }
    
    /**
     * @param int $isDel
     */
    public function setIsDel($isDel)
    {
        $this->isDel = $isDel;
    }
}
