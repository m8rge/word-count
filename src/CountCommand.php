<?php

namespace m8rge;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountCommand
{
    /**
     * @var array [word => appearanceCountInt, ...]
     */
    private $words = [];
    
    public function __invoke(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            throw new RuntimeException("File $filename doesn't exists");
        }

        if (!is_file($filename)) {
            throw new RuntimeException("$filename isn't a file");
        }

        $f = fopen($filename, 'rb');
        if (!$f) {
            throw new RuntimeException("Can't open $filename");
        }

        $this->words = [];
        $buffer = '';
        while ($block = fread($f, 4096)) {
            $buffer .= $block;
            $lastSpace = strrpos($buffer, ' ');
            if ($lastSpace > 0) {
                $part = substr($buffer, 0, $lastSpace);
                $buffer = substr($buffer, $lastSpace+1);
                $wordsCount = $this->countWords($part);
                $this->addWords($wordsCount);
            }
        }

        $wordsCount = $this->countWords($buffer);
        $this->addWords($wordsCount);

        array_multisort(array_values($this->words), SORT_DESC, array_keys($this->words), SORT_ASC, $this->words);
        foreach ($this->words as $word => $count) {
            $output->writeln("$word $count");
        }
    }

    private function addWords(array $wordsCount): void
    {
        foreach ($wordsCount as $word => $count) {
            if (array_key_exists($word, $this->words)) {
                $this->words[$word] += $count;
            } else {
                $this->words[$word] = $count;
            }
        }
    }

    private function countWords(string $string): array
    {
        preg_match_all('/\p{L}+/u', $string, $matches);
        $wordsCount = array_map('mb_strtolower', $matches[0]);
        $wordsCount = array_count_values($wordsCount);

        return $wordsCount;
    }
}