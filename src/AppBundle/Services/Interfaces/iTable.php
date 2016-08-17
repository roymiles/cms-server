<?php

interface iTable
{
    public function get(array $options, array $filters = ['limit' => 1, 'orderby' => 'ASC']);
    public function add(array $item);
    public function delete($obj);
    public function update($obj, array $options);
}
