<?php

namespace prgTW\BeanstalkToolbox\Command;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CopyCommand extends Command
{
	const ATTR_SRC_HOST = 'src-host';
	const ATTR_DST_HOST = 'dst-host';
	const OPT_SRC_PORT  = 'src-port';
	const OPT_DST_PORT  = 'dst-port';

	/** {@inheritdoc} */
	protected function configure()
	{
		parent::configure();
		$this->setName('copy');
		$this->addArgument(self::ATTR_SRC_HOST, InputArgument::REQUIRED, 'Source host');
		$this->addArgument(self::ATTR_DST_HOST, InputArgument::REQUIRED, 'Destination host');
		$this->addOption(self::OPT_SRC_PORT, null, InputOption::VALUE_REQUIRED, 'Source port', PheanstalkInterface::DEFAULT_PORT);
		$this->addOption(self::OPT_DST_PORT, null, InputOption::VALUE_REQUIRED, 'Destination port', PheanstalkInterface::DEFAULT_PORT);
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$srcHost = $input->getArgument(self::ATTR_SRC_HOST);
		$dstHost = $input->getArgument(self::ATTR_DST_HOST);
		$srcPort = $input->getOption(self::OPT_SRC_PORT);
		$dstPort = $input->getOption(self::OPT_DST_PORT);

		$src = new Pheanstalk($srcHost, $srcPort);
		$dst = new Pheanstalk($dstHost, $dstPort);

		foreach ($src->listTubes() as $tube)
		{
			var_dump($tube);
		}
	}
}
