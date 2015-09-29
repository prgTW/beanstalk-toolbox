<?php

namespace prgTW\BeanstalkToolbox\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
	const MANIFEST_FILE = 'http://prgtw.github.io/beanstalk-toolbox/manifest.json';

	/** {@inheritdoc} */
	protected function configure()
	{
		$this->setName('update');
		$this->setAliases([
			'self-update',
			'selfupdate',
		]);
		$this->setDescription('Updates @output@ to the latest version');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$manager = new Manager(Manifest::loadFile(self::MANIFEST_FILE));
		$manager->update($this->getApplication()->getVersion(), true);
	}
}
