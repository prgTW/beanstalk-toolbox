<?php

namespace prgTW\BeanstalkToolbox\Command;

use Pheanstalk\Pheanstalk;
use Pheanstalk\PheanstalkInterface;
use Pheanstalk\Response\ArrayResponse;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsCommand extends Command
{
	const ATTR_HOST    = 'host';
	const OPT_PORT     = 'port';
	const OPT_SORT     = 'sort';
	const OPT_ORDER    = 'order';
	const OPT_NO_ZEROS = 'no-zeros';

	/** {@inheritdoc} */
	protected function configure()
	{
		parent::configure();
		$this->setName('stats');
		$this->setDescription('Shows tubes primary statistics');
		$this->addArgument(self::ATTR_HOST, InputArgument::REQUIRED, 'Source host');
		$this->addOption(self::OPT_PORT, null, InputOption::VALUE_REQUIRED, 'Source port', PheanstalkInterface::DEFAULT_PORT);
		$this->addOption(self::OPT_SORT, null, InputOption::VALUE_REQUIRED, 'Source port', 'ready');
		$this->addOption(self::OPT_ORDER, null, InputOption::VALUE_REQUIRED, 'Sort order', -1);
		$this->addOption(self::OPT_NO_ZEROS, null, InputOption::VALUE_NONE, 'Don\'t show zeros');
	}

	/** {@inheritdoc} */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$srcHost = $input->getArgument(self::ATTR_HOST);
		$srcPort = $input->getOption(self::OPT_PORT);
		$sort    = $input->getOption(self::OPT_SORT);
		$order   = $input->getOption(self::OPT_ORDER);
		$noZeros = $input->getOption(self::OPT_NO_ZEROS);

		$columns = [
			'name'                  => 'name',
			'current-jobs-ready'    => 'ready',
			'current-jobs-reserved' => 'reserved',
			'current-jobs-delayed'  => 'delayed',
			'current-jobs-buried'   => 'buried',
		];

		$src = new Pheanstalk($srcHost, $srcPort);

		$table = new TableHelper(false);
		$table->setLayout(TableHelper::LAYOUT_BORDERLESS);
		$table->setHeaders($columns);

		$tubeNames = $src->listTubes();
		ksort($tubeNames);

		$data = [];
		foreach ($tubeNames as $tube)
		{
			/** @var ArrayResponse $response */
			$response = $src->statsTube($tube);
			$tubeData = $response->getArrayCopy();
			$tubeData = array_intersect_key($tubeData, $columns);
			if ($noZeros)
			{
				foreach ($tubeData as $key => $value)
				{
					if ('0' === $value)
					{
						$tubeData[$key] = '';
					}
				}
			}
			$data[] = $tubeData;
		}

		$column = array_search($sort, $columns);
		uasort($data, function (array $a1, array $a2) use ($column, $order)
		{
			return strnatcmp($a1[$column], $a2[$column]) * $order;
		});
		$table->addRows($data);
		$table->render($output);
	}
}
