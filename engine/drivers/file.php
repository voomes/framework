<?php

namespace drivers;

	class file
	{
		private $pointer, $file, $mode;
		
		public $content, $error;
		
		public function set($file)
		{
			$this->file = $file;
		}

		private function open($mode)
		{
			if(file_exists($this->file) && $mode == 'r' || $mode == 'r+' || $mode == 'w')
			{
				if($mode == 'x' || $mode == 'x+')
				{
					$this->error = "File: $this->file already exists.";
					return false;
				}
				else
				{
					$this->pointer = fopen($this->file, $mode);
					
					if($this->pointer)
					{
						return true;
					}
					else
					{
						$this->error = "File: $this->file could not be opened in mode: $mode".".";
						return false;
					}
				}
			}
			else
			{
				$this->error = "File: $this->file does not exists.";
				return false;
			}
		}
		
		public function copy($file,$newfile)
		{
			if(copy($file,$newfile))
			{
				return true;
			}
			else
			{
				$this->error = "Could not copy file: $file to location: $newfile".".";
				return false;
			}
		}
		
		public function write($input)
		{
			if($this->open('w') != false)
			{
				if(fwrite($this->pointer, $input))
				{
					return true;
				}
				else
				{
					$this->error = 'Could not write to file pointer.';
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		
		public function read()
		{
			$this->content = '';
			
			if($this->open('r') != false)
			{
				while(!feof($this->pointer))
				{
					$this->content .= fread($this->pointer, 4096);
				}
				
				return true;
			}
			else
			{
				return false;
			}
		}

		public function delete()
		{
			if(file_exists($this->file))
			{
				if(unlink($this->file))
				{
					return true;
				}
			}
			else
			{
				$this->error = "File: this->file does not exists.";
				return false;
			}
		}
	}
	
?>