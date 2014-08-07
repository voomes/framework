<?php

abstract class configuration
{
	public function load()
	{				  
		$variables = json_decode(file_get_contents("engine/globals/configuration.json"), 1);
				
		foreach($variables as $key => &$var)
		{
			$this->{$key} = &$var;
		}
	}
}

?>