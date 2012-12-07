<?php

require_once ('Base.php');

/**
 *
 * @author pedrotobo
 *        
 *        
 */
class Notification extends Base {
	public static $email = 'no-reply@utoronto.ca';
	public static $sender = 'Tutor System';
	
	/**
	 * Connects to database
	 *
	 * @return Zend_Db_Adapter_Abstract
	 */
	private static function database() {
		$config = new Zend_Config ( include 'services/config.php' );
		
		try {
			$db = Zend_Db::factory ( $config->database );
			// 'options' => array('buffer_results' => true)));
		} catch ( Zend_Db_Adapter_Exception $e ) {
			/*
			 * $logger ->log( $e->getFile() . '(' . $e->getLine() . ') - (' .
			 * $e->getCode() . ') ' . $e->getMessage(), Zend_Log::ERR);
			 */
		}
		
		return $db;
	}
	
	/**
	 * Sends an activation notice to a new student user
	 *
	 * @param string $userlogin        	
	 * @param string $username        	
	 * @param string $key        	
	 */
	public static function activationNotice($userlogin, $username, $key) {
		$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><p>Hi ' . $username . ',</p>
		<p>Thank you for joining us. 

		To finish activating your account and start using our service, click the activation link: 

		<a href="' . SITE_URL . 'activate.php?id=' . $key . '">' . SITE_URL . 'activate.php?id=' . $key . '</a></p>
		<p> Tutor Team!</p>';
		
		try {
			$mail = new Zend_Mail ();
			$mail->setFrom ( self::$email, self::$sender );
			$mail->addTo ( $userlogin );
			$mail->setSubject ( 'Activation Link' );
			$mail->setBodyHtml ( $html );
			$mail->send ();
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
		}
	}
	/**
	 * Sends approval waiting notification to a new tutor user
	 *
	 * @param string $userlogin        	
	 * @param string $username        	
	 */
	public static function approvalNotice($userlogin, $username) {
		$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><p>Hi ' . $username . ',</p>
		<p>Thank you for joining us.
	
		To finish activating your account our Administrator must approve your account, please wait for approval.</p>
		<p> Tutor Team!</p>';
		
		try {
			$mail = new Zend_Mail ();
			$mail->setFrom ( self::$email, self::$sender );
			$mail->addTo ( $userlogin );
			$mail->setSubject ( 'Waiting for approval' );
			$mail->setBodyHtml ( $html );
			$mail->send ();
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
		}
	}
	/**
	 * Sends a message
	 *
	 * @param string $from        	
	 * @param string $to        	
	 * @param string $subject        	
	 * @param string $message        	
	 * @return boolean
	 */
	public static function sendMessage($from, $to, $subject, $message) {
		try {
			Zend_Db_Table::setDefaultAdapter ( Notification::database () );
			$tbl = new Zend_Db_Table ( 'Messages' );
			
			$tbl->insert ( array (
					'from' => $from,
					'to' => $to,
					'subject' => $subject,
					'message' => $message,
					'status' => 'u',
					'date' => date ( 'Y-m-d h:i:s' ) 
			) );
			
			$html = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><p>Hi ' . $from . ',</p>
			<p>You have a new message from ' . $to . '
				
			<p> Tutor Team!</p>';
			
			$mail = new Zend_Mail ();
			$mail->setFrom ( self::$email, self::$sender );
			$mail->addTo ( $to );
			$mail->setSubject ( 'New message!!' );
			$mail->setBodyHtml ( $html );
			$mail->send ();
			
			return true;
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
			return false;
		}
	}
	/**
	 * Returns a user's message
	 *
	 * @param string $to
	 * @return Message
	 */
	public static function getMessage($id) {
		try {
			$db = Notification::database ();
			$select = $db->select()->from ( 'Messages' )->where (  "message_id = ?", $id );
			$rowset = $select->query()->fetchAll ();
				
			foreach ( $rowset as $row ) {
				$rs = new Message ();
	
				$rs->id = $row ['message_id'];
				$rs->from = $row ['from'];
				$rs->to = $row ['to'];
				$rs->subject = $row ['subject'];
				$rs->message = $row ['message'];
				$rs->status = $row ['status'];
				$rs->date = $row ['date'];
			}
				
			return $rs;
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
			return null;
		}
	}
	/**
	 * Returns all user's message
	 * 
	 * @param unknown_type $to
	 * @return Message[]|NULL
	 */
	public static function getMessages($to) {
		try {
			$db = Notification::database ();
			$select = $db->select()->from ( 'Messages' )->where (  "`to` = ?", $to );
			$rowset = $select->query()->fetchAll ();
			
			$res [] = new Message ();
			
			foreach ( $rowset as $row ) {
				$rs = new Message ();
				
				$rs->id = $row ['message_id'];
				$rs->from = $row ['from'];
				$rs->to = $row ['to'];
				$rs->subject = $row ['subject'];
				$rs->message = $row ['message'];
				$rs->status = $row ['status'];
				$rs->date = $row ['date'];
				
				array_push ( $res, $rs );
			}
			
			array_shift ( $res );
			
			return $res;
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
			return null;
		}
	}
	
	/**
	 * Marks a message as read
	 *
	 * @param int $id        	
	 * @return boolean
	 */
	public static function markRead($id) {
		try {
			Zend_Db_Table::setDefaultAdapter ( Notification::database () );
			$tbl = new Zend_Db_Table ( 'Messages' );
			
			$data = array (
					'status' => 'r' 
			);
			
			$where = $tbl->getAdapter ()->quoteInto ( 'message_id = ?', $id );
			$tbl->update ( $data, $where );
			
			return true;
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
			return false;
		}
	}
	
	/**
	 * Deletes a message
	 * 
	 * @param int $id
	 * @return boolean
	 */
	public static function deleteMesage($id) {
		try {
			Zend_Db_Table::setDefaultAdapter ( Notification::database () );
			$tbl = new Zend_Db_Table ( 'Messages' );
			
			$where = $tbl->getAdapter ()->quoteInto ( 'message_id = ?', $id );
			$tbl->delete ( array (
					$where 
			) );
			
			return true;
		} catch ( Zend_Exception $e ) {
			// $this->logger->log ( $e->getFile () . '(' . $e->getLine () . ') -
			// (' . $e->getCode () . ') ' . $e->getMessage (), Zend_Log::ERR );
			return false;
		}
	}
}
class Message {
	/**
	 *
	 * @var int id
	 */
	public $id;
	
	/**
	 *
	 * @var string $from
	 */
	public $from;
	
	/**
	 *
	 * @var string $to
	 */
	public $to;
	
	/**
	 *
	 * @var string $subject
	 */
	public $subject;
	
	/**
	 *
	 * @var string $message
	 */
	public $message;
	
	/**
	 *
	 * @var string $status
	 */
	public $status;
	
	/**
	 *
	 * @var string $date
	 */
	public $date;
}

?>