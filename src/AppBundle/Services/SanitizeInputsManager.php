<?php
// src/AppBundle/Services/SanitizeInputsManager.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

// To be removed...
class SanitizeInputsManager
{   
    // Is this class really needed anymore?
    public function getValidOrder($order){
        if($order === null){ return "ASC"; }
        $order = strtolower($order);
        if($order == "descending"){
            return "DESC";
        }else{
            return "ASC";
        }
    }
}