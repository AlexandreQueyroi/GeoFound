<?php

declare(strict_types=1);



namespace GrahamCampbell\ResultType;


abstract class Result
{
    
    abstract public function success();

    
    abstract public function map(callable $f);

    
    abstract public function flatMap(callable $f);

    
    abstract public function error();

    
    abstract public function mapError(callable $f);
}
