<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\DomCrawler\Crawler;

abstract class ImportAbstract
{
    protected $data = null;

    public function getData($source)
    {
        // temporarily use local file
        if (($contents = file_get_contents($source, 'r')) !== false) {
            $this->data = new Crawler($contents);
        }

        return $this->data;
    }

    abstract function run();
}

