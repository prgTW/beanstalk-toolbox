<?php

use prgTW\BeanstalkToolbox\Command;

require_once __DIR__ . '/../vendor/autoload.php';


$application = new \Symfony\Component\Console\Application('Beanstalk Toolbox', '@git-version@');

$application->addCommands([
	new Command\CopyCommand,
	new Command\StatsCommand,
	new Command\UpdateCommand,
]);
$application->run();
