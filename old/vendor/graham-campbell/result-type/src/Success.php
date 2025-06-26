<?php

declare(strict_types=1);



namespace GrahamCampbell\ResultType;

use PhpOption\None;
use PhpOption\Some;


final class Success extends Result
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
        return Some::create($this->value);
    }

    
    public function map(callable $f)
    {
        return self::create($f($this->value));
    }

    
    public function flatMap(callable $f)
    {
        return $f($this->value);
    }

    
    public function error()
    {
        return None::create();
    }

    
    public function mapError(callable $f)
    {
        return self::create($this->value);
    }
}
