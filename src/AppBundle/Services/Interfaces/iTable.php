<?php

namespace AppBundle\Services\Interfaces;

interface iTable
{
    public function get(array $options, array $filters);
    public function add(array $item);
    public function delete($obj);
    public function update($obj, array $options);
}
