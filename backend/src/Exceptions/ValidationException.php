<?php

namespace App\Exceptions;

class ValidationException extends \Exception
{
    public function errorMessage(): string
    {
        return 'Error on line '.$this->getLine().' in '.$this->getFile()
            .': <b>'.$this->getMessage();
    }
}