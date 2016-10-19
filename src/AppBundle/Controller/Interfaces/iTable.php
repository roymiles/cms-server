<?php

namespace AppBundle\Controller\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface iTable
{
    // Show the table / render twig template for generic inline table
    public function getAction(Request $request);
    
    // Post request only to add a row to the table
    public function addAction(Request $request);
    
    // Post request only to delete a row in the table
    public function deleteAction(Request $request);
    
    // Post request only to update a row in the table
    public function updateAction(Request $request);
    
    public function isColumn(string $columnName, string $flags);
}
