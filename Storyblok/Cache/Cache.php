<?php

namespace Storyblok\Cache;

use Doctrine\Common\Cache\FilesystemCache;

/**
* Cache Wrapper
*/
class Cache
{
    private $cacheDriver;
    private $cachePath;

    public function __construct() {
        
    }

    public function setCachePath($path)
    {
        $this->cacheDriver = new FilesystemCache($path);
        $this->cacheDriver->setNamespace('storyblok_');
    }

    public function fetch($id)
    {
        return $this->cacheDriver->fetch($id);
    }

    public function contains($id)
    {
        if (!$this->cacheDriver) {
            return false;
        }

        return $this->cacheDriver->contains($id);
    }

    public function save($id, $data)
    {
        if (!$this->cacheDriver) {
            return false;
        }

        return $this->cacheDriver->save($id, $data);
    }


}