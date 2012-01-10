<?php
	class CDatabaseController {
		private static $instance = null;
		private $db = null;
		public $error = '';
		
		private function __construct() {
			$this->db = 
				new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_DATABASE);
	
			if(mysqli_connect_error()) {
				die('Could not connect to database');
			}
			
			$this->db->set_charset('utf-8');
		}
		
		/*
		  Singleton, allows us to work with a single connection 
		  throughout the page.
		*/
		public static function getInstance() {
			if(static::$instance == null) {
				static::$instance = new CDatabaseController;
			}
			
			return static::$instance;
		}
		
		public function query($query) {
			$this->error = '';
			
			$result = $this->db->query($query);
			
			if($this->db->errno != 0) {
				$this->error = $this->db->error;
			}
			
			return $result;
		}
		
		public function multiQuery($query) {
			$this->error = '';
			
			$result = $this->db->multi_query($query);
		
			if($this->db->errno != 0) {
				$this->error = $this->db->error;
			}
			
			return $result;
		}
		
		public function retrieveAndStoreResultsFromMultiQuery(&$statements) {
			$i = 0;
			$results = array();
			$this->error = '';
			
			do {
				$results[$i++] = $this->db->store_result();
				
				if($this->db->errno != 0)
					$this->error = $this->db->error;
			
			} while($this->db->more_results() && $this->db->next_result());
		
			$statements = $i;
			
			return $results;
		}
		
		public function retrieveAndIgnoreResultsFromMultiQuery() {
			$i = 0;
			$this->error = '';
			
			do {
				$this->db->store_result();
				
				if($this->db->errno != 0)
					$this->error = $this->db->error;
				
				$i++;
			} while($this->db->more_results() && $this->db->next_result());
			
			return $i;
		}
		
		public function escapeString($value) {
			return $this->db->real_escape_string($value);
		}
	}
?>
