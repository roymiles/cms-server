<?php
// src/AppBundle/Services/SanitizeInputsManager.php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Services;

class SanitizeInputsManager
{   
    public function getValidOrder($order){
        $order = strtolower($order);
        if($order == "descending"){
            return "DESC";
        }else{
            return "ASC";
        }
    }
}