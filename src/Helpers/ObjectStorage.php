<?php

namespace Mchuluq\Laravel\Uac\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

trait ObjectStorage {

    function getObjectStorageKey(){
        $key = $this->attributes[$this->primaryKey];
        return md5(self::class.$this->table.$key);
    }

    private function hasPk(){
        return isset($this->attributes[$this->primaryKey]);
    }

    private function _getCache($default=array()){
        $key = $this->getObjectStorageKey();
        return Cache::store(config('uac.object_storage_driver'))->get($key,$default);
    }
    private function _writeCache($data=null){
        $key = $this->getObjectStorageKey();
        return Cache::store(config('uac.object_storage_driver'))->forever($key,$data);
    }
    
    function getStorage($data_key,$default=null){
        if(!$this->hasPk()){
            return;
        }
        $data = $this->_getCache();
        return Arr::get($data,$data_key,$default);
    }
    function setStorage($key,$val=null){
        if(!$this->hasPk()){
            return;
        }
        $data = $this->_getCache();
        if(is_array($key)){
            $data = array_merge($data,$key);
        }else{
            Arr::set($data, $key,$val);
        }
        return $this->_writeCache($data);
    }
    function unsetStorage($key){
        if(!$this->hasPk()){
            return;
        }
        $data = $this->_getCache();
        Arr::forget($data,$key);
        return $this->_writeCache($data);
    }
    function destroyStorage(){
        if(!$this->hasPk()){
            return;
        }
        $key = $this->getKey();
        return Cache::store(config('uac.object_storage_driver'))->pull($key);       
    }
    function getAllStorage(){
        if(!$this->hasPk()){
            return;
        }
        return $this->_getCache();
    }
}