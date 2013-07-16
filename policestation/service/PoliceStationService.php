<?php

namespace policestation\service;

$projbasedir = $_SESSION["basedir"];
require_once( realpath($projbasedir."/exception/errorlog/DatabaseException.php") );
require_once( realpath($projbasedir."/exception/errorlog/SessionException.php") );
require_once( realpath($projbasedir."/utils/ErrorLog.php") );

use policestation\exception\errorlog\DatabaseException as DatabaseException;
use policestation\exception\errorlog\SessionException as SessionException;
use policestation\utils\ErrorLog as ErrorLog;

require_once( realpath($projbasedir."/dbinterface/Database.php") );
require_once($projbasedir."/dbinterface/MySqlDatabase.php");
require_once($projbasedir."/dbinterface/PostgresDatabase.php");

abstract class PoliceStationService {
	
	public function execute() {
		$database = $this->getDatabase();
		
		/*
		   I think there must be a connect for each php page.
		   In order to hide that from the application side i think establishing the connection here is the best choice.
		*/
		$database->connect();
		
		$database->start_transaction();
		try {
			$this->dispatch();
		} catch(DatabaseException $d) {
			$database->rollback();
			ErrorLog::log( $d->getErrorType(), $d->getFile(), $d->getLine(), $d->getMessage());
			$database->close_all_connections();
			exit(ErrorPages::databaseErrorPage($d->getMessage()));
		} catch(SessionException $s) {
			$database->rollback();
			ErrorLog::log( $s->getErrorType(), $s->getFile(), $s->getLine(), $s->getMessage());
			$database->close_all_connections();
			exit(ErrorPages::sessionErrorPage($s->getMessage()));
		} catch(Exception $e) {
			$database->rollback();
			$database->close_connection();
			throw $e;
		}
		$database->commit();
		$database->close_connection();
	}
	
	private function getDatabase() {
		return $_SESSION["database"];
	}
	
	public function getGame() {
		return $_SESSION["game"];
	}

	protected abstract function dispatch();
}
?>
