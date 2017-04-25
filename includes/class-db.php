<?php

class DB {
	// Database connection information
	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpassword;

	private $pdo;

	private $table;

	public function __construct( $dbhost, $dbname, $dbuser, $dbpassword ) {
		$this->dbhost = $dbhost;
		$this->dbname = $dbname;
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;

		$this->db_connect();
	}

	/**
	 * Database connect and Create connection
	 */
	private function db_connect() {
		try {
			$dsn = "mysql:host={$this->dbhost};dbname={$this->dbname};charset=" . DB_CHARSET;
			$collate = ! empty( DB_COLLATE ) ? ' COLLATE ' . DB_COLLATE : '';
			$options = array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHARSET . $collate
			);

			// Create pdo instance
			$this->pdo = new PDO( $dsn , $this->dbuser, $this->dbpassword, $options );

			if ( DEBUG === 'production' ) {
				// 에러 출력하지 않음
				$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
			}
			else if ( DEBUG === 'development' ) {
				// 에러 출력
				$this->pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			}
		}
		catch ( PDOException $e ) {
			echo 'DB 연결 중 에러가 발생했습니다.';
			exit;
		}
	}

	/**
	 * Mysql query execute
	 *
	 * @param ( String ) $query
	 */
	private function execute( $query ) {
		try {
			$this->pdo->query( $query );
		}
		catch ( PDOException $e ) {
			debug( $e->getMessage() );
			debug( $query );
		}
	}

	/**
	 * Mysql table setting
	 *
	 * @param ( String ) $tablename
	 */
	public function table( $tablename ) {
		$this->table = $tablename;
	}

	/**
	 * Mysql insert query
	 *
	 * @param ( String ) $tablename
	 * @param ( array ) $sets
	 * @see $this->execute( String )
	 */
	public function insert( $tablename = '', $sets = array() ) {
		$target_table = $this->table;

		if ( gettype( $tablename ) === 'array' ) {
			$sets = $tablename;
		}
		else if ( gettype( $tablename ) === 'string' ) {
			$target_table = $tablename;
		}

		$columns = "";
		$values = "";

		foreach ( $sets as $key=>$value ) {
			$columns .= "{$key},";
			$values .= "'" . htmlspecialchars( $value ) . "',";
		}
		$columns = substr( $columns, 0, -1 );
		$values = substr( $values, 0, -1 );

		// Query execute
		$this->execute( "INSERT INTO {$target_table} ( {$columns} ) VALUES ( {$values} )" );
	}

	public function select( $tablename = '', $columns = array() ) {

	}
}

$db = new DB( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );
