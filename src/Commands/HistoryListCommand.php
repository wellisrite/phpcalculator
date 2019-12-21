<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

class HistoryListCommand extends CalculatorCommand
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
        return 'history:list';
    }

    protected function getOperator(): string
    {
        return 'list';
    }

    protected function getFirstArgumentField(): string
    {
        return 'commands';
    }

    protected function getSignature(): string
    {
        $string = sprintf(
            "%s {%s?* : Filter the history by commands}",
            $this->getCommandVerb(),
            $this->getFirstArgumentField()
        );

        return $string;
    }

    public function getDescription(): string 
    {
        $string = 'Show saved history';

        return $string;
    }

}