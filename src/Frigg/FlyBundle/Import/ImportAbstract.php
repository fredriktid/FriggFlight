<?php

namespace Frigg\FlyBundle\Import;

use Symfony\Component\DomCrawler\Crawler;

abstract class ImportAbstract
{
    protected $data = array();

    protected function request($source)
    {
        if (($content = file_get_contents($source, 'r')) !== false) {
            $this->data = simplexml_load_string($content);
        }

        return $this->data;
    }

    abstract public function __toString();
    abstract public function run();
}

