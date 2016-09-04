<?php

namespace AppBundle\Services;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

use AppBundle\Entity\Logs;

class LoggerManager extends Controller
{   
    private $em;
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Logs';
    }    
    
    protected $logLevels = array(
        'EMERGENCY' => 0,
        'ALERT'     => 1,
        'CRITICAL'  => 2,
        'ERROR'     => 3,
        'WARNING'   => 4,
        'NOTICE'    => 5,
        'INFO'      => 6,
        'DEBUG'     => 7
    );
    
    private function add($message, $logLevel, $data = null){
        $log = new Logs();
        
        $message = serialize($message);
        $data = serialize($data);
        
        $log->setMessage($message);
        $log->setData($data);
        $log->setDateTime(new \DateTime());
        
        // Tells Doctrine you want to (eventually) save the Log (no queries yet)
        $this->em->persist($log);
        
        // Actually executes the queries (i.e. the INSERT query)
        $this->em->flush();   
        
        return true;
    }
    
    public function error($message, $data = null){
        return $this->add($message, $this->logLevels['ERROR'], $data);
    }
}
