<?php
/**
 * Created by PhpStorm.
 * User: Baihuzi
 * Date: 2018/1/31
 * Time: 14:52
 */

namespace LianYun\Passport\Entities\Repositories;

use Doctrine\ORM\EntityRepository;
use LianYun\Passport\Common\CommonFunc;

class BaseRepository extends EntityRepository
{
    /**
     * @param        $class
     * @param string $where
     * @param array  $parameters
     * @param array  $groups
     * @param array  $order
     * @param int    $first
     * @param int    $limit
     *
     * @return \Doctrine\ORM\Query
     */
    public function getList($class, $where = "", $parameters = [], $groups = [], $order = [], $first = 0, $limit = 1000)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(['e'])->from($class['select'], 'e');
        
        if ($class['left']) {
            $qb->addSelect("left")
               ->leftJoin($class['left'], 'left');
        }
        if ($where) {
            $qb->where($where);
            foreach ($parameters as $key => $val) {
                $qb->setParameter($key, $val);
            }
        }
        if (is_array($groups) && sizeof($groups)) {
            $qb->groupBy(array_shift($groups));
            foreach ($groups as $group) {
                $qb->addGroupBy($group);
            }
        }
        if (is_array($order) && sizeof($order)) {
            $i = 0;
            foreach ($order as $item => $sort) {
                if ($i > 0) {
                    $qb->addOrderBy($item, $sort);
                }
                else {
                    $qb->orderBy($item, $sort);
                }
                $i++;
            }
        }
        
        $qb->setFirstResult($first);
        $qb->setMaxResults($limit);
        
        $this->getEntityManager()->getConnection()->executeQuery(
            "set SESSION sql_mode ='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION ';"
        );
        
        return $qb->getQuery();
    }
    
    public function queryCount($class, $where = [], $parameters = [], $arg = [])
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select([$qb->expr()->count("e.id")])->from($class['select'], 'e');
        if ($class['left']) {
            $qb->addSelect("left")
               ->leftJoin($class['left'], 'left');
        }
        if ($where) {
            $qb->where($where);
            foreach ($parameters as $key => $val) {
                $qb->setParameter($key, $val);
            }
        }
        
        $qb->setMaxResults(1);
        
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * 更新数据
     *
     * @param $entity
     */
    public function refreshData($entity)
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
    
    /**
     * @param $keys
     *
     * @return object|null
     */
    public function getData($keys)
    {
        $data_info = $this->findOneBy($keys);
        
        return $data_info;
    }
    
    /**
     * 返回当前时间戳 保持统一
     *
     * @return int
     */
    public function getNowTime()
    {
        if (!$this->now) {
            $this->now = time();
        }
        
        return $this->now;
    }
    
    public function removeData($entity)
    {
        $this->getEntityManager()->remove($entity);
        $this->flush();
    }
    
    public function persist($entity)
    {
        $this->getEntityManager()->persist($entity);
    }
    
    public function flush($entity = null)
    {
        $this->getEntityManager()->flush($entity);
    }
    
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }
    
}
