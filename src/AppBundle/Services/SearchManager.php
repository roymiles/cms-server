<?php
// src/AppBundle/Services/SearchManager.php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\EntityManager;

use AppBundle\Services\Interfaces;

class SearchManager
{
    private $repository;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->repository = 'AppBundle\Entity\Users';
    }
    
    // List of all searchable tables and their call back functions for performing a search
    protected $searchableTables = [
      'AppBundle\Entity\Users' => 'searchUsers',
      'AppBundle\Entity\ForumPosts' => 'searchForumPosts'
    ];

    public function search(string $query, array $tables, int $maxResults = 10){
      // Filter out all the invalid tables from the parameters
      $tablesToSearch = array_intersect($tables, array_keys($this->searchableTables));
      $results = array();
      foreach($tablesToSearch as $table){
        array_push($results, call_user_func($searchableTables[$table]));
      }
  
      // Sort the results by rank
      usort($results, function($a, $b) {
          return $a['rank'] <=> $b['rank'];
      });
      
      return [
        'totalResults' => sizeof($results), // Used for pagination
        'results' => array_slice($results, $maxResults)
      ];
      
    }
    
    private function searchUsers(){
      $rankQuery = "
      
      ";
      
      $titleQuery = "
        SELECT * FROM ...
      ";
      
      $descriptionQuery = "
      
      ";
      
      return $searchTable($rankQuery, $titleQuery, $descriptionQuery);
    }
    
    private function searchTable($rankQuery, $titleQuery, $descriptionQuery){
      $subLimit = 50;
    
      // Running an sql query directly as its more flexible than doctrine
      // http://stackoverflow.com/questions/12862389/symfony2-doctrine-create-custom-sql-query
      $connection = $this->em->getConnection();
      $statement = $connection->prepare("
        SELECT searchData.rank,
               searchData.title,
               searchData.description
        FROM(
          SELECT(
            (
              SELECT 1
            ) AS rank,
            (
              /* Generate a title from the selected row */
              SELECT "title"
            ) AS title,
            (
              /* Generate a description from the selected row */
              SELECT "description"
            ) AS description
          )
        ) AS searchData
        ORDER BY searchData.rank DESC
        LIMIT :limit
      ");
      $statement->bindValue('limit', $subLimit);
      $statement->execute();
      $results = $statement->fetchAll();
      return $results;
    }
    
}
