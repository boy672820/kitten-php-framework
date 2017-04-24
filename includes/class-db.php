<?php

class DB {
	// Database connection information
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpassword;

	private $pdo;

	private $tablename;

	public function __construct( $dbhost, $dbname, $dbuser, $dbpassword ) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;

		$this->db_connect();
	}

	private function db_connect() {
		try {
			$dsn = "mysql:host={$dbhost};dbname={$dbname};charset=" . DB_CHARSET;
			$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHARSET . ' COLLATE ' . DB_COLLATE
			);

			// Create pdo instance
			$this->pdo = new PDO( $dsn , $dbuser, $dbpassword, $options );

			// 에러 출력하지 않음
			// $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
			// Warning만 출력
			// $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			// 에러 출력
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch ( Exception $e ) {
			echo $e->getMessage();
			exit;
		}
	}

	public function set_table( $tablename ) {
		$this->tablename = $tablename;
	}

	public function insert( $tablename = '', $sets = array() ) {

		$target_tablename = '';

		if ( empty( $tablename ) ) {
			if ( empty( $this->tablename ) ) {
				return false;
			}
			$target_tablename = $this->tablename;
		}
		else {
			$target_tablename = $tablename;
		}

		$columns = "";
		$values = "";

		foreach ( $sets as $key=>$value ) {
			$columns .= "{$key},";
			$values .= "'" . htmlspecialchars( $value ) . "',";
		}
		$columns = substr( $columns, 0, -1 );
		$values = substr( $values, 0, -1 );

		$query = "INSERT INTO {$target_tablename} ( {$columns} ) VALUES ( {$values} )";
	}
}


$db = new DB( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );
$db->insert( PREFIX . 'posts', array(
	'title' => 'title',
	'content' => 'content'
) );
$db->select( PREFIX . 'posts',
	array(
		'title',
		'content'
	),
	array(
		'compare' => 'like',
		'created_at' => ''
	)
);
