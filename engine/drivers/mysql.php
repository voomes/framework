<?php

namespace drivers;

class mysql
{
	public $result, $table, $instance, $connected;
	public $lastQuery, $lastId;
	
	public $databaseprefix = '';
	
	public function __construct($table="")
	{
		$this->connect();
		$this->table = $this->databaseprefix.$table;
	}
	
	public function __destruct()
	{
		if(@get_resource_type($this->instance) == "mysql")
		{
			$this->close();
			unset($this->instance);
		}
		else
		{
			unset($this->instance);
		}
	}
	
	
	public function set($table)
	{
		$this->table =  $this->databaseprefix.$table;
	}
	
	public function connect($database=false,$host=false,$username=false,$password=false)
	{
		if(!empty($host))
		{
			$this->instance = \mysql_connect($host, $username, $password);
		}
		else
		{
			$this->instance = \mysql_connect(host, username, password);
		}
		
		if($this->connected == 1)
		{
			if($database == false)
			{
				return $this->instance;
			}
			else
			{
				if(mysql_select_db($database, $this->instance))
				{
					return $this->instance;
				}
			}
		}
		else
		{
			if($this->instance)
			{
				if($database == false)
				{
					if(mysql_select_db(database, $this->instance))
					{
						$this->connected = 1;
						return true;
					}
				}
				else
				{
					if(mysql_select_db($database, $this->instance))
					{
						$this->connected = 1;
						return true;
					}
				}
			}
			else
			{
				echo mysql_error(); exit();
			}
		}
	}
		
	public function select($what, $where1, $where2, $and="", $operator="=")
	{
		unset($this->result);
				
		if(empty($where1) && empty($where2))
		{
			$mask = "SELECT {what}";
		}
		else
		{
			$mask = "SELECT {what} FROM $this->table WHERE {w1} $operator '{w2}' {and}";
		}		
		
		if(!empty($and))
		{
			$temp = str_replace(" ", "", $and);
			$temp = explode("'",$temp);
			$size = sizeof($temp);
			
			for($i=0;$i<$size;$i++)
			{
				if($i % 2)
				{
					$and = str_replace($temp[$i], mysql_real_escape_string($temp[$i]), $and);
				}
			}
		}

		$where2 = mysql_real_escape_string($where2);
		$mask = str_replace("{what}", $what, str_replace("{w1}", $where1, str_replace("{w2}", $where2, str_replace("{and}", $and, $mask))));
		$this->lastQuery = $mask;
		
		if($query = @mysql_query($mask))
		{
			$i = 0;
			
			while($row = mysql_fetch_array($query))
			{
				$this->result[$i] = $row;
				$i++;
			}
			

			if($i == 1)
			{
				$this->result = $this->result[0];
				$this->rows = 1;
			}
			else
			{
				if(empty($this->result))
				{
					return false;
				}
				
				$this->rows = $i;
			}
			
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function inverse()
	{
		if($this->rows == 1)
		{
			$temporary = $this->result;
			unset($this->result);
			
			$this->result = array($temporary);
		}
	}	
	
	public function insert($params,$getId='')
	{
		if(!empty($params) && isset($this->table))
		{
			$mask = "INSERT INTO $this->table({rows}) VALUES({values})";
			
			$row = "";
			$val = "";
			
			if(!empty($getId))
			{
				$params['tablelock'] = microtime(true);
			}
			
			foreach($params as $key => $value)
			{
				$row = $row . $key . ",";
				$val = $val ."\"". mysql_real_escape_string($value) . "\"" . ",";
			}
			
			$row = substr($row, 0, -1);
			$val = substr($val, 0, -1);
			
			$mask = str_replace("{rows}", $row, str_replace("{values}", $val, $mask));
						
			if(@mysql_query($mask))
			{
				if(!empty($getId))
				{
					if($this->select($getId,'tablelock',$params['tablelock']))
					{
						$this->lastId = $this->result[$getId];
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return true;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function compute($what1, $what2, $where1, $where2, $sign="", $and="", $operator="=")
	{
		if(isset($this->table))
		{
			$mask = "UPDATE $this->table SET {what1} = {what1} $sign {what2} WHERE {where1} {operator} '{where2}' {and}";
			
			if(!empty($and))
			{
				$temp = str_replace(" ", "", $and);
				$temp = explode("'",$temp);
				$size = sizeof($temp);
				
				for($i=0;$i<$size;$i++)
				{
					if($i % 2)
					{
						$and = str_replace($temp[$i], mysql_real_escape_string($temp[$i]), $and);
					}
				}
			}
			
			$mask = str_replace("{what1}", mysql_real_escape_string($what1), str_replace("{what2}", mysql_real_escape_string($what2), str_replace("{where1}", mysql_real_escape_string($where1), str_replace("{where2}", mysql_real_escape_string($where2), str_replace("{and}", $and, str_replace('{operator}',$operator,$mask))))));
						
			if(@mysql_query($mask))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function update($what1, $what2, $where1, $where2, $and="", $operator="=")
	{
		if(isset($this->table))
		{
			$mask = "UPDATE $this->table SET {what1} = '{what2}' WHERE {where1} {operator} '{where2}' {and}";
			
			if(!empty($and))
			{
				$temp = str_replace(" ", "", $and);
				$temp = explode("'",$temp);
				$size = sizeof($temp);
				
				for($i=0;$i<$size;$i++)
				{
					if($i % 2)
					{
						$and = str_replace($temp[$i], mysql_real_escape_string($temp[$i]), $and);
					}
				}
			}
			
			$mask = str_replace("{what1}", mysql_real_escape_string($what1), str_replace("{what2}", mysql_real_escape_string($what2), str_replace("{where1}", mysql_real_escape_string($where1), str_replace("{where2}", mysql_real_escape_string($where2), str_replace("{and}", $and, str_replace('{operator}',$operator,$mask))))));
						
			if(@mysql_query($mask))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function delete($where1, $where2, $and="", $comparation="")
	{
		if(isset($this->table))
		{
			if(!empty($and))
			{
				$temp = str_replace(" ", "", $and);
				$temp = explode("'",$temp);
				$size = sizeof($temp);
				
				for($i=0;$i<$size;$i++)
				{
					if($i % 2)
					{
						$and = str_replace($temp[$i], mysql_real_escape_string($temp[$i]), $and);
					}
				}
			}
			
			if(!empty($comparation))
			{
				$mask = "DELETE FROM $this->table WHERE {where1} $comparation {where2} $and'"; 
			}
			else
			{
				$mask = "DELETE FROM $this->table WHERE {where1} = '{where2}' $and";
			}
					
			$mask = str_replace("{where1}", $where1, str_replace("{where2}", $where2, $mask));
						
						
			
			if(@mysql_query($mask))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function replace($fields, $what)
	{
		if(isset($this->table))
		{
			if(!empty($and))
			{
				$temp = str_replace(" ", "", $and);
				$temp = explode("'",$temp);
				$size = sizeof($temp);
				
				for($i=0;$i<$size;$i++)
				{
					if($i % 2)
					{
						$and = str_replace($temp[$i], mysql_real_escape_string($temp[$i]), $and);
					}
				}
			}
			
			
			$mask = "REPLACE INTO $this->table ({fields}) VALUES ({what})"; 

			$mask = str_replace("{fields}", $fields, str_replace("{what}", $what, $mask));
			
			if(@mysql_query($mask))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	public function escape($string, $instance)
	{
		$this->connect();

		$valid = mysql_real_escape_string($string, $this->instance);

		if($valid)
		{
			return $valid;
		}
		else
		{
			return false;
		}
	}
	
	public function query($query)
	{
		if(mysql_query($query))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function close()
	{
		if(mysql_ping($this->instance))
		{
			if(mysql_close($this->instance))
			{
				$this->connected = 0;
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}

?>