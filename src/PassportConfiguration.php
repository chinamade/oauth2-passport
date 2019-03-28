<?php

namespace LianYun\Passport;

use Amopi\Mlib\Utils\StringUtils;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Created by Mopi.
 *
 * Date: 2019-01-07
 * Time: 10:40
 */
class PassportConfiguration implements ConfigurationInterface
{
    
    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root        = $treeBuilder->root('app');
        {
            $root->children()->booleanNode('is_debug')->defaultValue(true);
            $root->children()->scalarNode('secret')->isRequired();
            $root->children()->scalarNode('token_url')->isRequired();
            $root->children()->integerNode('token_lifetime');
            $root->children()->integerNode('external_token_lifetime');
            $root->children()->integerNode('refresh_token_lifetime');
            $root->children()->scalarNode('token_iss');
            
            $dir = $root->children()->arrayNode('dir');
            {
                $makeAbsolute = function ($path) {
                    return StringUtils::stringStartsWith($path, \DIRECTORY_SEPARATOR)
                        ? $path
                        : \PROJECT_DIR . \DIRECTORY_SEPARATOR . $path;
                };
                
                $dir->children()->scalarNode('log')->beforeNormalization()->always($makeAbsolute);
                $dir->children()->scalarNode('data')->beforeNormalization()->always($makeAbsolute);
                $dir->children()->scalarNode('cache')->beforeNormalization()->always($makeAbsolute);
                $dir->children()->scalarNode('template')->beforeNormalization()->always($makeAbsolute);
            }
            
            $db = $root->children()->arrayNode('db');
            {
                $db->children()->scalarNode('host')->isRequired();
                $db->children()->integerNode('port')->defaultValue(3306);
                $db->children()->scalarNode('user')->isRequired();
                $db->children()->scalarNode('password')->isRequired();
                $db->children()->scalarNode('dbname')->isRequired();
                $db->children()->scalarNode('prefix')->isRequired();
            }
            $subdomains = $root->children()->arrayNode('subdomains');
            {
                $subdomains->children()->scalarNode('oauth')->isRequired();
                $subdomains->children()->scalarNode('server')->isRequired();
            }
            
        }
        
        return $treeBuilder;
    }
}

