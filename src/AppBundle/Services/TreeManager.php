<?php
// src/AppBundle/Services/TreeManager.php

namespace AppBundle\Services;

use AppBundle\Entity\Documentation;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;


class WrapperNode { 
    function __construct($documentation){
        $this->doc = $documentation;
        $this->children = [];
    }
}

class TreeManager
{
    /*
        [
            1 => [
                // I am the parent
                "parent" => object
                "children" => [
                    1 => [
                      ...
                    ]

                    2 => [
                      ...
                    ]
                ]
            ]

            2 => [
                // etc another parent
            ]
        ]
     */
    public function makeTree($objects, string $parentReferenceName){
        $getParentFunctionName = 'get'.$parentReferenceName;
        
        $map = [];
        $parentNodes = [];
        
        foreach($objects as $key => $object){
            $map[$object->getId()] = new WrapperNode($object);
        }
        
        foreach($objects as $key => $object){
            $node = $map[$object->getId()];
            $parent = call_user_func_array(array($object, $getParentFunctionName), array());
            
            if($parent == null){
                array_push($parentNodes, $node);
            }else{
                $parentNode = $map[$parent->getId()];
                array_push($parentNode->children, $node);
            }
        }
        
        return $parentNodes;
    }
    
    public function mergeChildren(){}
}