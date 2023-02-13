<?php 

class Userfunction{

	private $DBHOST='localhost';
	private $DBUSER='root';
	private $DBPASS='';
	private $DBNAME='ajax';
	public $con;

	public function __construct(){
		$this->con = mysqli_connect($this->DBHOST, $this->DBUSER, $this->DBPASS, $this->DBNAME);
		if(!$this->con){
			return false;
		}
	}

	public function htmlvalidation($form_data){
		$form_data = trim( stripslashes( htmlspecialchars( $form_data ) ) );
		$form_data = mysqli_real_escape_string($this->con, trim(strip_tags($form_data)));
		return $form_data;
	}

	public function insert($tblname, $filed_data){

		$query_data = "";

		foreach ($filed_data as $q_key => $q_value) {
			$query_data = $query_data."$q_key='$q_value',";
		}
		$query_data = rtrim($query_data,",");

		$query = "INSERT INTO $tblname SET $query_data";
		$insert_fire = mysqli_query($this->con, $query);
		if($insert_fire){
			return $insert_fire;
		}
		else{
			return false;
		}

	}

	public function select_assoc($tblname, $condition, $op='AND'){

		$field_op = "";
		foreach ($condition as $q_key => $q_value) {
			$field_op = $field_op."$q_key='$q_value' $op ";
		}
		$field_op = rtrim($field_op,"$op ");

		$select_assoc = "SELECT * FROM $tblname WHERE $field_op";
		$select_assoc_query = mysqli_query($this->con, $select_assoc);
		if(mysqli_num_rows($select_assoc_query) > 0){
			if(mysqli_num_rows($select_assoc_query) == 1)
			{
				$select_assoc_fire = mysqli_fetch_assoc($select_assoc_query);
				if($select_assoc_fire){
					return $select_assoc_fire;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		else{	
			return false;
		}

	}

	public function select($tblname){

		$select = "SELECT * FROM $tblname";
		$select_fire = mysqli_query($this->con, $select);
		if(mysqli_num_rows($select_fire) > 0){
			$select_fetch = mysqli_fetch_all($select_fire, MYSQLI_ASSOC);
			if($select_fetch){
				return $select_fetch;
			}
			else{
				return false;
			}
		}
		else{
			return false;
		}
	}

	public function update($tblname, $field_data, $condition, $op='AND'){

		$field_row = "";
		foreach ($field_data as $q_key => $q_value) {
			$field_row = $field_row."$q_key='$q_value',";
		}
		$field_row = rtrim($field_row,",");

		$field_op = "";

		foreach ($condition as $q_key => $q_value) {
			$field_op = $field_op."$q_key='$q_value' $op ";
		}
		$field_op = rtrim($field_op,"$op ");

		$update = "UPDATE $tblname SET $field_row WHERE $field_op";
		$update_fire = mysqli_query($this->con, $update);
		if($update_fire){
			return $update_fire;
		}
		else{
			return false;
		}

	}	

	public function delete($tblname, $condition, $op='AND'){

		$delete_data = "";

		foreach ($condition as $q_key => $q_value) {
			$delete_data = $delete_data."$q_key='$q_value' $op ";
		}

		$delete_data = rtrim($delete_data,"$op ");		
		$delete = "DELETE FROM $tblname WHERE $delete_data";
		$delete_fire = mysqli_query($this->con, $delete);
		if($delete_fire){
			return $delete_fire;
		}
		else{
			return false;
		}

	}

	public function search($tblname,$search_val,$op="AND"){

		$search = "";
		foreach($search_val as $s_key => $s_value){
			$search = $search."$s_key LIKE '%$s_value%' $op ";
		}
		$search = rtrim($search, "$op ");

		$search = "SELECT * FROM $tblname WHERE $search";
		$search_query = mysqli_query($this->con, $search);
		if(mysqli_num_rows($search_query) > 0){
			$serch_fetch = mysqli_fetch_all($search_query, MYSQLI_ASSOC);
			return $serch_fetch;
		}
		else{
			return false;
		}

	}	

}
//function to prepare statement for insert, update, delete, select
function prepare_stmt($con, $sql, $params){
	$stmt = mysqli_stmt_init($con);
	if(!mysqli_stmt_prepare($stmt, $sql)){
		return false;
	}
	else{
		$types = "";
		foreach($params as $param){
			if(is_int($param)){
				$types .= "i";
			}
			elseif(is_string($param)){
				$types .= "s";
			}
			elseif(is_double($param)){
				$types .= "d";
			}
			else{
				$types .= "b";
			}
		}
		$bind_names[] = $types;
		for($i=0; $i<count($params); $i++){
			$bind_name = 'bind' . $i;
			$$bind_name = $params[$i];
			$bind_names[] = &$$bind_name;
		}
		call_user_func_array(array($stmt, 'bind_param'), $bind_names);
		return $stmt;
	}
}
//function to create a prepared statement for insert
function insert($con, $table, $data){
	$keys = array_keys($data);
	$values = array_values($data);
	$sql = "INSERT INTO $table (".implode(", ", $keys).") VALUES (".implode(", ", array_fill(0, count($values), "?")).")";
	$stmt = prepare_stmt($con, $sql, $values);
	if($stmt){
		mysqli_stmt_execute($stmt);
		return mysqli_stmt_insert_id($stmt);
	}
	else{
		return false;
	}
}
//function to create a prepared statement for update
function update($con, $table, $data, $where){
	$keys = array_keys($data);
	$values = array_values($data);
	$set = "";
	foreach($keys as $key){
		$set .= "$key=?, ";
	}
	$set = rtrim($set, ", ");
	$sql = "UPDATE $table SET $set WHERE $where";
	$stmt = prepare_stmt($con, $sql, $values);
	if($stmt){
		mysqli_stmt_execute($stmt);
		return mysqli_stmt_affected_rows($stmt);
	}
	else{
		return false;
	}
}
//function to create a prepared statement for delete
function delete($con, $table, $where){
	$sql = "DELETE FROM $table WHERE $where";
	$stmt = mysqli_query($con, $sql);
	if($stmt){
		return mysqli_affected_rows($con);
	}
	else{
		return false;
	}
}
?>
