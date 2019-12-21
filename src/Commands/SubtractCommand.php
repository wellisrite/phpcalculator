<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

class SubtractCommand extends CalculatorCommand
{
    
    public function __construct()
    {
        $signature = $this->getSignature();
        $description = $this->getDescription();
        $operator = $this->getOperator();

        parent::__construct($this->getCommandVerb(), $signature, $description, $operator);
    }

    protected function getCommandVerb(): string
    {
        return 'subtract';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'subtracted';
    }

    protected function getOperator(): string
    {
        return '-';
    }
    
    protected function getSignature(): string
    {
        $string = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $this->getCommandVerb(),
            $this->getCommandPassiveVerb()
        );

        return $string;
    }

    public function getDescription(): string 
    {
        $string = sprintf('%s all given Numbers', ucfirst($this->getCommandVerb()));

        return $string;
    }

}