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
	 * @param ( Array ) $sets
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

	/**
	 * Mysql select query
	 *
	 * @param ( String ) $tablename
	 * @param ( Array ) $fields
	 * @param ( Array ) $where
	 * @see $this->execute( String )
	 */
	public function select( $tablename = '', $fields = array(), $where = array() ) {
		$target_table = $this->table;

		$columns = '';
		foreach ( $fields as $field ) {
			$columns .= $field . ',';
		}
		$columns = substr( $columns, 0, -1 );

		$conditions = '';
		$i = 0;
		foreach ( $where as $key=>$obj ) {
			$next = $where[ $i ];

			if ( $key === 'relation' ) {
				foreach ( $next as $value ) {
					var_dump( $value );
					echo '<br />';
				}
				break;
			}

			if ( gettype( $obj ) === 'array' ) {
				echo $key;
				break;
			}

			$conditions .= $key . '=\'' . $obj . '\' AND ';
		}
		$conditions = substr( $conditions, 0, -5 );

		$query = "SELECT {$columns} FROM {$target_table} WHERE {$conditions}";
		// echo $query;
		$i += 1;
	}

	public function relation( $obj = array(), $relation = 'AND' ) {
		$obj_keys = array_keys( $obj );

		$i = -1;
		$count = count( $obj ) - 2;
		$query = '';
		$query_array = array();

		while ( $count >= $i ++ ) {
			$key = $obj_keys[ $i ];
			$value = $obj[ $key ];

			if ( $key === 'relation' ) {
				$query .= $this->relation( $obj[ $obj_keys[ $i + 1 ] ], $value );
				break;
			}

			if ( gettype( $value ) === 'array' ) {
				$query .= '(';
				$query .= $this->relation( $value );
				$query .= ')' . $relation;
				continue;
			}

			// $query .= $key . '=\'' . $value . '\' ' . $relation . ' ';
			array_push( $query_array, $key . '=\'' . $value . '\' ', $relation . ' ' );
		}
		array_pop( $query_array );
		$query .= $this->array_serialize_print( $query_array );

		return $query;
	}

	public function array_serialize_print( $array ) {
		$str = '';
		foreach ( $array as $var ) $str .= $var;
		return $str;
	}
}

$db = new DB( DB_HOST, DB_NAME, DB_USER, DB_PASSWORD );
var_dump(
	$db->relation(
		array(
			'relation' => 'OR',
			array(
				array(
					'type' => 'post',
					'status' => 'publish'
				),
				array(
					'title' => 'test'
				)
			)
		)
	)
);

// echo '<hr />';
//
// $db->relation(
// 		array(
// 			'type' => 'post',
// 			'status' => 'publish'
// 		)
// 	);


// $db->select(
// 		PREFIX . 'posts',
// 		array( 'title', 'contents', 'created_at' ),
// 		array(
// 			'relation' => 'OR',
// 			array(
// 				array(
// 					'type' => 'post',
// 					'status' => 'publish'
// 				),
// 				array(
// 					'compare' => 'like',
// 					array(
// 						'title' => '%Notice%'
// 					)
// 				)
// 			)
// 		)
// 	);
