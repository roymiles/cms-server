<?php
// src/AppBundle/Services/SanitizeManager.php
namespace AppBundle\Services;

use AppBundle\Entity\Sites;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Doctrine\ORM\EntityManager;

class SanitizeManager
{
  public function getValidOrder($order){
    $order = strtolower($order);
    switch($order){
      case 'asc':
      case 'ascending':
      case 'ascend':
        return 'ASC';
        break;
      
      case 'desc':
      case 'descending':
      case 'descend':
        return 'DESC';
        break;      
      
      default:
        return 'ASC';
        break;
    }
  }
  
  public function getValidSortBy($table, $sortBy){}
  
  public function getValidQuery($query){
  
  }
}
