<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

class PowCommand extends CalculatorCommand
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
        return 'pow';
    }

    protected function getOperator(): string
    {
        return '^';
    }

    protected function getFirstArgumentField(): string
    {
        return 'base';
    }

    protected function getSecondArgumentField(): string
    {
        return 'exp';
    }
    
    protected function getSignature(): string
    {
        $string = sprintf(
            "%s {%s : The base number}
                {%s : The exponent number}",
            $this->getCommandVerb(),
            $this->getFirstArgumentField(),
            $this->getSecondArgumentField()
        );

        return $string;
    }

    public function getDescription(): string
    {
        $string = 'Exponent the given number';

        return $string;
    }
}
