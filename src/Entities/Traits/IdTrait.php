<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2017/8/14
 * Time: 12:01
 */

namespace LianYun\Passport\Entities\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
}
