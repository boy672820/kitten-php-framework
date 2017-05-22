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
	public function select( $tablename = '', $fields = '*', $where = array() ) {
		$target_table = $this->table;

		$columns = '';
		if ( gettype( $fields ) === 'array' ) {
			foreach ( $fields as $field ) {
				$columns .= $field . ',';
			}
			$columns = substr( $columns, 0, -1 );
		}
		else if ( gettype( $fields ) === 'string' ) {
			$columns = $fields;
		}

		$condition = $this->relation( $where );


		$query = "SELECT {$columns} FROM {$target_table} WHERE {$condition}";
		// echo $query;
		$i += 1;
	}

	public function condition( $obj = array(), $attr = array( 'type' => 'relation', 'value' => 'AND' ) ) {
		$obj_keys = array_keys( $obj );

		$i = -1;
		$count = count( $obj ) - 2;
		$query = '';
		$query_array = array();

		$relation = $attr[ 'type' ] === 'relation' ? $attr[ 'value' ] : 'AND';
		$compare = $attr[ 'type' ] === 'compare' ? $attr[ 'value' ] : '=';

		while ( $count >= $i ++ ) {
			$key = $obj_keys[ $i ];
			$value = $obj[ $key ];

			if ( ! empty( $obj_keys[ $i + 1 ] ) ) {

				if ( $key === 'relation' ) {
					$query .= $this->condition( $obj[ $obj_keys[ $i + 1 ] ], array( 'type' => 'relation', 'value' => $value ) );
					break;
				}

				if ( $key === 'compare' ) {
					$query .= $this->condition( $obj[ $obj_keys[ $i + 1 ] ], array( 'type' => 'compare', 'value' => $value ) );
					break;
				}

			}

			if ( gettype( $value ) === 'array' ) {
				$query .= '(';
				$query .= $this->condition( $value );
				$query .= ') ' . $relation . ' ';
				continue;
			}

			array_push( $query_array, $key . ' ' . $compare . ' \'' . $value . '\'', ' ' . $relation . ' ' );
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

echo $db->condition(
		array(
			'relation' => 'OR',
			array(
				array(
					array(
						'compare' => 'like',
						array(
							'type' => '%post%',
						)
					),
					'compare' =>'tester'
				),
				'title' => 'test'
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
