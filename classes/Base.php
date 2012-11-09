<?php

/**
 * @author pedrotobo
 *
 *
 */

/** */
require_once 'services/common.php';

/** Libraries */
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();

class Base {
	/** Consumes the configuration array */
	protected $config;
	protected $db;
	protected $logger;
	protected $writer;

	function __construct() {
		$config = new Zend_Config(include 'services/config.php');

		$stream = @fopen('php://output', 'a', false);
		if (!$stream) {
			throw new Exception('Failed to open stream');
		}

		$writer = new Zend_Log_Writer_Stream($stream);
		$logger = new Zend_Log($writer);

		try {
			$this->db = Zend_Db::factory($config->database);
			//'options' => array('buffer_results' => true)));
		} catch (Zend_Db_Adapter_Exception $e) {
			$logger
					->log(
							$e->getFile() . '(' . $e->getLine() . ') - ('
									. $e->getCode() . ') ' . $e->getMessage(),
							Zend_Log::ERR);
		} catch (Zend_Exception $e) {
			$logger
					->log(
							$e->getFile() . '(' . $e->getLine() . ') - ('
									. $e->getCode() . ') ' . $e->getMessage(),
							Zend_Log::ERR);
		}
	}
}

?>