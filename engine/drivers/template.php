<?php

namespace drivers;

	use drivers as driver;

class template
{
	private $template;
	public $html;
	
	public function set($name)
	{
		$this->template = root.'templates/'.$name.'.html';
	}
			
	public function render()
	{		
		$file = new driver\file; 
		
		$file->set($this->template);
		
		if(!$file->read())
		{
			echo 'Driver <b>template</b> failed. Reason:'.$file->error;
			exit();
		}
		else
		{	
			foreach($this as $k => $v)
			{
				if($k == 'template' || $k == 'html')
				{
					continue;
				}
				
				$file->content = str_replace('<'.$k.'>', $v, $file->content);
				unset($this->{$k});
			}
			
			$this->html = $file->content;		
			unset($file);
		}
	}
}

?>