<?php

namespace m8rge;

use Silly\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GreetCommand
{
    function __invoke(Application $app, InputInterface $input, OutputInterface $output)
    {
        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $app->getHelperSet()->get('question');
        $question = new Question('Enter optional append text: ');
        $text = $helper->ask($input, $output, $question);

        $name = $input->getArgument('name');
        $greeting = $input->getOption('greeting');
        if (empty($greeting)) {
            $greeting = 'Hello, ';
        }
        $output->writeln("{$greeting}{$name}! {$text}");
    }
}