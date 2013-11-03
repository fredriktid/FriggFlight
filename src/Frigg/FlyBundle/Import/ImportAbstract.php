<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\DomCrawler\Crawler;

abstract class ImportAbstract
{
    protected $data = array();

    public function request($source)
    {
        if (($content = file_get_contents($source, 'r')) !== false) {
            $this->data = simplexml_load_string($content);
        }

        return $this->data;
    }

    abstract function __toString();
    abstract function run();
}

