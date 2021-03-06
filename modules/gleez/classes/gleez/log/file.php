<?php defined('SYSPATH') or die('No direct script access.');
/**
 * File log writer
 *
 * Writes out messages and stores them in a YYYY/MM directory.
 *
 * @package    Gleez\Logging
 * @author     Sandeep Sangamreddi - Gleez
 * @copyright  (c) 2011-2013 Gleez Technologies
 * @license    http://gleezcms.org/license  Gleez CMS License
 */
class Gleez_Log_File extends Log_Writer {

	/**
	 * @var  string  Directory to place log files in
	 */
	protected $_directory;

	/**
	 * Creates a new file logger
	 *
	 * Checks that the directory exists and is writable.
	 *
	 * Example:<br>
	 * <code>
	 *   $writer = new Log_File($directory);
	 * </code>
	 *
	 * @param   string  Log directory
	 * @throws  Kohana_Exception
	 * @uses    System::mkdir
	 * @uses    Debug::path
	 */
	public function __construct($directory)
	{
		if ( ! is_dir($directory))
		{
			try
			{
				// Create the cache directory
				System::mkdir($directory);
			}
			catch (Exception $e)
			{
				throw new Kohana_Exception('Could not create cache directory :dir',
					array(':dir' => Debug::path($directory)));
			}
		}

		if ( ! is_writable($directory))
		{
			throw new Kohana_Exception('Directory :dir must be writable',
				array(':dir' => Debug::path($directory)));
		}

		// Determine the directory path
		$this->_directory = realpath($directory).DIRECTORY_SEPARATOR;
	}

	/**
	 * Writes each of the messages into the log file
	 *
	 * The log file will be appended to the `YYYY/MM/DD.log.php` file,
	 * where YYYY is the current year, MM is the current month,
	 * and DD is the current day.
	 *
	 * Example:<br>
	 * <code>
	 *   $writer->write($messages);
	 * </code>
	 *
	 * @param   array  $messages  Log messages
	 * @throws  Kohana_Exception
	 * @uses    System::mkdir
	 * @uses    Debug::path
	 */
	public function write(array $messages)
	{
		// Set the yearly directory name
		$directory = $this->_directory.date('Y');

		if ( ! is_dir($directory))
		{
			try
			{
				// Create the cache directory
				System::mkdir($directory);
			}
			catch (Exception $e)
			{
				throw new Kohana_Exception('Could not create cache directory :dir',
					array(':dir' => Debug::path($directory)));
			}
		}

		// Add the month to the directory
		$directory .= DIRECTORY_SEPARATOR.date('m');

		if ( ! is_dir($directory))
		{
			try
			{
				// Create the yearly directory
				System::mkdir($directory);
			}
			catch (Exception $e)
			{
				throw new Kohana_Exception('Could not create cache directory :dir',
					array(':dir' => Debug::path($directory)));
			}
		}

		// Set the name of the log file
		$filename = $directory.DIRECTORY_SEPARATOR.date('d').EXT;

		if ( ! file_exists($filename))
		{
			// Create the log file
			file_put_contents($filename, Kohana::FILE_SECURITY.' ?>'.PHP_EOL);

			// Allow anyone to write to log files
			chmod($filename, 0666);
		}

		$info = array(
			'hostname'   => Request::$client_ip,
			'user_agent' => Request::$user_agent,
			'url'        => Request::$initial->uri(),
			'referer'    => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
		);

		foreach ($messages as $message)
		{
			// Write each message into the log file
			// Format: time --- level: body hostname
			file_put_contents($filename, PHP_EOL.$message['time'].' - - '.$this->_log_levels[$message['level']].': '.$message['body'].' - - '.$info['hostname'].' - - '.$info['url'].' - - '.$info['user_agent'].' - - '.$info['referer'], FILE_APPEND);
		}

	}

}
