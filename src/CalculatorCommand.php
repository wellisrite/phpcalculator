<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Exception;

class CalculatorCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;
    
    protected $operator;
    protected $name;

    public function __construct($name, $signature, $description, $operator)
    {
        $this->name = $name;
        $this->signature = $signature;
        $this->description = $description;
        $this->operator = $operator;

        parent::__construct();
    }

    public function handle(): void
    {
        $numbers = $this->getInput();
        $description = $this->generateCalculationDescription($numbers);
        $result = $this->calculateAll($numbers);
        $output = sprintf('%s = %s', $description, $result);

        $this->comment($output);
    }

    protected function getInput(): array
    {
        $numbers = $this->argument('numbers');
        $index = 0;
        
        try {
            $filteredInput = array_map(function ($number) use ($index, $numbers) {
                $input = preg_replace('/[^0-9]/', '', $number);
                if (!$input) {
                    throw new Exception("Arguments not valid");
                }
                return $input;
            }, $numbers);

            return $filteredInput;
        } catch (\Exception $e) {
            echo $e->getMessage()."\n";
            exit;
        }
    }

    protected function generateCalculationDescription(array $numbers): string
    {
        $operator = $this->operator;
        $glue = sprintf(' %s ', $operator);

        return implode($glue, $numbers);
    }

    /**
     * @param array $numbers
     *
     * @return float|int
     */
    protected function calculateAll(array $numbers)
    {
        $number = array_pop($numbers);

        if (count($numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($numbers), $number);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        switch ($this->operator) {
            case "+":
                return $number1 + $number2;
                break;
            case "-":
                return $number1 - $number2;
                break;
            case "*": 
                return $number1 * $number2;
                break;
            case "/": 
                return $number1 / $number2;
                break;
            default:
                echo "Unknown Operator";
                exit;
        }
    }
}
