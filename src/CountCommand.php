<?php

namespace m8rge;

use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CountCommand
{
    function __invoke(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            throw new RuntimeException("File $filename doesn't exists");
        } elseif (!is_file($filename)) {
            throw new RuntimeException("$filename isn't a file");
        }

        $f = fopen($filename, 'r');
        if (!$f) {
            throw new RuntimeException("Can't open $filename");
        }

        $words = [];
        $addWords = function($string) use (&$words) {
            preg_match_all('/\p{L}+/u', $string, $matches);
            $blockWords = array_map('mb_strtolower', $matches[0]);
            $blockWords = array_count_values($blockWords);

            array_walk($words, function (&$value, $word) use ($blockWords) {
                if (array_key_exists($word, $blockWords)) {
                    $value += $blockWords[$word];
                }
            });
            $words += $blockWords;
        };
        
        $buffer = '';
        while ($block = fread($f, 4096)) {
            $buffer .= $block;
            $lastSpace = strrpos($buffer, ' ');
            if ($lastSpace > 0) {
                $part = substr($buffer, 0, $lastSpace);
                $buffer = substr($buffer, $lastSpace+1);
                $addWords($part);
            }
        }

        $addWords($buffer);

        array_multisort(array_values($words), SORT_DESC, array_keys($words), SORT_ASC, $words);
        foreach ($words as $word => $count) {
            $output->writeln("$word $count");
        }
    }
}