<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Exception;

class CalculatorCommand extends Command
{
    const CSV_FILE_NAME = "history.csv";
    const CSV_HEADER = ['No', 'Command', 'Description', 'Result', 'Output', 'Time'];
    const DEFAULT_DELIMITER = ',';
    const HISTORY_LIST = "list";
    const HISTORY_CLEAR = "clear";

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
        if (!file_exists($this->getFilePath())) {
            touch($this->getFilePath());
        }
        
        $this->name = $name;
        $this->signature = $signature;
        $this->description = $description;
        $this->operator = $operator;
        
        parent::__construct();
    }

    private function getFilePath(): string
    {
        return __DIR__.self::CSV_FILE_NAME;
    }

    public function handle(): void
    {
        if ($this->operator == self::HISTORY_LIST) {
            $filters = $this->getInput() ?: [];
            $header = self::CSV_HEADER;
            $rows = $this->readHistory($filters);
            $output = !empty($rows) ? $this->table($header, $rows) : "History is empty.";
        } elseif ($this->operator == self::HISTORY_CLEAR) {
            $status = HistoryHelper::clearHistory();
            $output =  $status ? "History cleared!" : "History not cleared.";
        } else {
            $numbers = $this->getInput();
            $description = $this->generateCalculationDescription($numbers);
            $result = $this->calculateAll($numbers);
            $output = sprintf('%s = %s', $description, $result);

            $this->writeHistory([
                'command' => ucfirst($this->name),
                'description' => $description,
                'result' => $result,
                'output' => $output
            ]);
        }

        $this->comment($output);
    }

    protected function getInput(): array
    {
        switch ($this->operator) {
            case "^":
                $numbers = [ $this->argument('base'), $this->argument('exp') ];
                break;
            case self::HISTORY_LIST:
                $numbers = $this->argument('commands');
                return $numbers;
            case self::HISTORY_CLEAR:
                $numbers = $this->argument('commands');
                return $numbers;
            default:
                $numbers = $this->argument('numbers');
                break;
        }
        
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
            case "^":
                return $number1 ** $number2;
                break;
            default:
                echo "Unknown Operator";
                exit;
        }
    }

    public function getHistoryLastNumber(): int
    {
        $rows = file($this->getFilePath());
        $n = count($rows)+1;
        return (int) $n;
    }

    public function readHistory(array $filters = []): array
    {
        $csvFile =  $this->getFilePath();
        $fileHandle = fopen($csvFile, 'r');
        $lineText = [];
        $filters = array_map('strtolower', $filters);

        while (!feof($fileHandle)) {
            $row = fgetcsv($fileHandle, 0, self::DEFAULT_DELIMITER);
            if ((in_array((strtolower($row[1])), $filters, 1) || empty($filters)) && $row) {
                array_push($lineText, $row);
            }
        }

        fclose($fileHandle);
        
        return $lineText;
    }

    public function writeHistory(array $data = []): bool
    {
        date_default_timezone_set('Asia/Jakarta');
        $csvFile =  self::getFilePath();
        $number = 1;
        $line = [
            $number,
            $data['command'],
            $data['description'],
            $data['result'],
            $data['output'],
            date("Y-m-d H:i:s")
        ];

        $number = $this->getHistoryLastNumber();
        $fileHandle = fopen($csvFile, 'a');
        $line[0] = $number;
        $status = fputcsv($fileHandle, $line);

        fclose($fileHandle);

        return $status;
    }

    public function clearHistory(): bool
    {
        $status = unlink($this->getFilePath());

        return $status;
    }
}
