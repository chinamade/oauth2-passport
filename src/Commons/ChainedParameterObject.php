<?php
/**
 * Created by PhpStorm.
 * User: lbc
 * Date: 19/06/2017
 * Time: 11:36
 */

namespace LianYun\Passport\Commons;

use Amopi\Mlib\Http\ChainedParameterBagDataProvider;
use Amopi\Mlib\Utils\AbstractDataProvider;
use Amopi\Mlib\Utils\Exceptions\DataValidationException;
use Amopi\Mlib\Utils\Exceptions\MandatoryValueMissingException;
use Amopi\Mlib\Utils\Validators\ValidatorInterface;

class ChainedParameterObject extends ChainedParameterBagDataProvider
{
    
    public function getMandatory($key, $type = self::STRING_TYPE)
    {
        $keys = explode('.', $key);
        if (sizeof($keys) >= 1) {
            return $this->getMultipleValue($key, $type);
        }
        
        try {
            $result = parent::getMandatory($key, $type);
        } catch (\Exception $e) {
            throw $e;
        }
        
        if (empty($result) && $type == self::STRING_TYPE) {
            throw (new MandatoryValueMissingException("Mandatory value $key can not be empty in data"))
                ->withFieldName($key);
        }
        
        return $result;
    }
    
    public function getOptional($key, $type = self::STRING_TYPE, $default = '')
    {
        $keys = explode('.', $key);
        if (sizeof($keys) >= 1) {
            try {
                return $this->getMultipleValue($key, $type);
            } catch (\Exception $e) {
                return $default;
            }
        }
        
        return parent::getOptional($key, $type, $default); // TODO: Change the autogenerated stub
    }
    
    private function getMultipleValue($key, $type)
    {
        $result = $this->getByName($key);
        
        try {
            if (!$type instanceof ValidatorInterface) {
                $type = $this->getValidatorByLegacyString($type);
            }
            $result = $type->validate($result);
            
            return $result;
        } catch (DataValidationException $e) {
            $e->setFieldName($key);
            throw $e;
        }
    }
    
    private function getByName($name)
    {
        $keys = explode('.', $name);
        
        foreach ($this->bags as $bag) {
            if (!$bag->has($keys[0])) {
                continue;
            }
            $vaule = $bag->get($keys[0]);
            unset($keys[0]);
            
            foreach ($keys as $key) {
                $vaule = $this->getValueByKey($key, $vaule);
                if (null === $vaule) {
                    return null;
                }
            }
            
            return is_string($vaule) ? trim($vaule) : $vaule;
            
        }
        
        return null;
    }
    
    private function getValueByKey($key, $array = [])
    {
        if (isset($array[$key])) {
            return $array[$key];
        }
        else {
            return null;
        }
    }
    
}
