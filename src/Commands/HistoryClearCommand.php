<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

class HistoryClearCommand extends CalculatorCommand
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
        return 'history:clear';
    }

    protected function getFirstArgumentField(): string
    {
        return 'commands';
    }

    protected function getOperator(): string
    {
        return 'clear';
    }

    protected function getSignature(): string
    {
        $string = $this->getCommandVerb();

        return $string;
    }

    public function getDescription(): string
    {
        return 'Clear saved history';
    }

}