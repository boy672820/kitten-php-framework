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
			$dsn = "mysql:host={$this->dbhost};dbname={$this->dbname};charset=" . DB_CHARSET;
			$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHARSET . ' COLLATE ' . DB_COLLATE
			);

			// Create pdo instance
			$this->pdo = new PDO( $dsn , $this->dbuser, $this->dbpassword, $options );

			// 에러 출력하지 않음
			// $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
			// Warning만 출력
			// $pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
			// 에러 출력
			$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		}
		catch ( PDOException $e ) {
			echo 'DB 연결 중 에러가 발생했습니다.';
			echo $e->getMessage();
		}
	}

	public function set_table( $tablename ) {
		$this->tablename = $tablename;
	}

	public function insert( $tablename = '', $sets = array() ) {
		$target_tablename = '';

		if ( empty( $tablename ) ) {
			if ( empty( $this->tablename ) ) {
				throw new Exception( 'Empty table name..' );
			}
			$target_tablename = $this->tablename;

			if ( count( $sets ) <= 0 ) {
				$sets = $tablename;
			}
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
		echo $query;
	}
}


$db = new DB( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );
// $db->insert( PREFIX . 'posts', array(
// 	'title' => 'title',
// 	'content' => 'content'
// ) );
