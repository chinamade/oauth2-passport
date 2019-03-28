<?php
/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:40
 */

namespace LianYun\Passport\Database;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use LianYun\Passport\Passport;
use LianYun\Passport\Commons\OrmTablePrefix;

class ApiDatabase
{
    public static function getEntityManager()
    {
        static $entityManager = null;
        if ($entityManager instanceof EntityManager) {
            return $entityManager;
        }
        
        $app = Passport::app();
        
        $isDevMode = $app->isDebug();
        $config    = Setup::createAnnotationMetadataConfiguration(
            [PROJECT_DIR . "/src/Entities"],
            $isDevMode,
            $app->getParameter('app.dir.data') . "/proxies",
            null,
            false /* do not use simple annotation reader, so that we can understand annotations like @ORM/Table */
        );
        $config->addEntityNamespace("Api", "GoldSdk\\Api\\Entities");
        //$config->setSQLLogger(new \Doctrine\DBAL\Logging\EchoSQLLogger());
        
        $conn           = $app->getParameter('app.db');
        $conn["driver"] = "pdo_mysql";
        
        // $connectionOptions and $config set earlier
        $evm         = new \Doctrine\Common\EventManager;
        $tablePrefix = new OrmTablePrefix($app->getParameter('app.db.prefix'));
        $evm->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, $tablePrefix);
        $entityManager = EntityManager::create($conn, $config, $evm);
        
        return $entityManager;
    }
    
    public static function getDatabaseConnection(Configuration $configuration = null)
    {
        static $connection = null;
        if ($connection instanceof Connection
            && $connection->isConnected()
        ) {
            return $connection;
        }
        $app            = Passport::app();
        $conn           = $app->getParameter('app.db');
        $conn["driver"] = "pdo_mysql";
        
        $connection = DriverManager::getConnection($conn, $configuration);
        
        return $connection;
    }
    
}
