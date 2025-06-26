<?php

declare(strict_types=1);



namespace GrahamCampbell\ResultType;

use PhpOption\None;
use PhpOption\Some;


final class Error extends Result
{
    
    private $value;

    
    private function __construct($value)
    {
        $this->value = $value;
    }

    
    public static function create($value)
    {
        return new self($value);
    }

    
    public function success()
    {
        return None::create();
    }

    
    public function map(callable $f)
    {
        return self::create($this->value);
    }

    
    public function flatMap(callable $f)
    {
        
        return self::create($this->value);
    }

    
    public function error()
    {
        return Some::create($this->value);
    }

    
    public function mapError(callable $f)
    {
        return self::create($f($this->value));
    }
}
