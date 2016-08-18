<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// src/AppBundle/Twig/AppExtension.php
namespace AppBundle\Twig;

use AppBundle\Libraries\JBBCode\Parser;
use AppBundle\Libraries\JBBCode\DefaultCodeDefinitionSet;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('bbcode', array($this, 'bbCodeParse')),
        );
    }

    public function bbCodeParse($text)
    {
        $parser = new Parser();
        $parser->addCodeDefinitionSet(new DefaultCodeDefinitionSet());
        $parser->parse($text);
        return $parser->getAsHtml();
    }

    public function getName()
    {
        return 'app_extension';
    }
}