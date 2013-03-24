<?php

/**
 * Stopwatch debuger panel
 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
 * @see http://addons.nette.org/cs/nextensions/stopwatch
 */
final class Stopwatch extends \Nette\Application\UI\Control implements \Nette\Diagnostics\IBarPanel
{
	/** @var array $timers */
	private static $timers = array();

	/** @var array $description */
	private static $description = array();

	/**
	 * Constructor
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @param \Nette\Application\Application $application
	 * @return void
	 */
	public function __construct(\Nette\Application\Application $application)
	{
		parent::__construct($application->getPresenter(),  $this->getId());
	}

	/**
	 * Return panel ID
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @return string
	 */
	public function getId()
	{
		return __CLASS__;
	}

	/**
	 * Html code for DebugerBar Tab
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @return string
	 */
	public function getTab()
	{
		$template = $this->getFileTemplate(__DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tab.latte');
		$template->sum = $this->getStopwatchesSummary();
		return $template;
	}

	/**
	 * Html code for DebugerBar Panel
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @return string
	 */
	public function getPanel()
	{
		$template = $this->getFileTemplate(__DIR__.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'panel.latte');
		$template->timers = self::$timers;
		$template->description = self::$description;
		$template->sum = $this->getStopwatchesSummary();
		return $template;
	}

	/**
	 * Return summary of all stopwatches
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @return string
	 */
	private function getStopwatchesSummary()
	{
		return number_format(round(array_sum(self::$timers), 1), 1);
	}

	/**
	 * Start stopwatch
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @param string $name
	 * @param string $description
	 * @return void
	 */
	public static function start($name = NULL, $description = NULL)
	{
		\Nette\Diagnostics\Debugger::timer($name);
		self::$description[$name !== NULL ? $name : uniqid()] = $description;
	}

	/**
	 * Stop stopwatch
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @param string $name
	 * @return string
	 */
	public static function stop($name = NULL)
	{
		$time = \Nette\Diagnostics\Debugger::timer($name);
		self::$timers[$name !== NULL ? $name : uniqid()] = number_format(round($time * 1000, 1), 1);;
		return $time;
	}

	/**
	 * Load template file path with aditional macros and variables
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @param string $templateFilePath
	 * @return \Nette\Templating\FileTemplate
	 * @throws \Nette\FileNotFoundException
	 */
	private function getFileTemplate($templateFilePath)
	{
		if(file_exists($templateFilePath))
		{
			$template = new \Nette\Templating\FileTemplate($templateFilePath);
			$template->onPrepareFilters[] = callback($this, 'templatePrepareFilters');
			$template->registerHelperLoader('Nette\Templating\Helpers::loader');
			$template->basePath = realpath(__DIR__);
			return $template;
		}
		else
		{
			throw new \Nette\FileNotFoundException('Requested template file is not exist.');
		}
	}

	/**
	 * Load latte and set aditional macros
	 * @author Pavel Železný <pavel.zezlenzy@socialbakers.com>
	 * @param \Nette\Templating\Template $template
	 * @return void
	 */
	public function templatePrepareFilters($template)
	{
		$template->registerFilter($latte = new \Nette\Latte\Engine());
		$set = \Nette\Latte\Macros\MacroSet::install($latte->getCompiler());
		$set->addMacro('src', NULL, NULL, 'echo \'src="\'.\Nette\Templating\Helpers::dataStream(file_get_contents(%node.word)).\'"\'');
	}
}