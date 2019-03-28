<?php

namespace LianYun\Passport;

use Doctrine\DBAL\Connection;
use GuzzleHttp\Client;
use Amopi\Mopi\Mopi;
use Doctrine\ORM\EntityManager;

/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:40
 */
class Passport extends Mopi
{
    public function getHttpKernel()
    {
        return parent::getHttpKernel();
    }
    
    public function getValidator()
    {
        return $this->getHttpKernel()['Validator'];
    }
    
    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getService("em");
    }
    
    /**
     * @return Connection
     */
    public function getDatabasesConnection()
    {
        return $this->getService("db.connection");
    }
    
    
    public function getClient()
    {
        return new Client(['timeout'=>8]);
    }
}

