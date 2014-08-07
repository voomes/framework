<?php

	namespace drivers;
	namespace applications;

		use drivers as driver;
		use applications as app;
		
	date_default_timezone_set('America/Los_Angeles');
		
	class time
	{
		public function convert($timestamp)
		{
			$seconds = time()-$timestamp;
			
			
			if($seconds == 0)
			{
				$time = 'Few moments ago.';
			}
			else
			{
				switch($seconds)
				{
					case $seconds < 60:
					
						$time = 'Few moments ago.';
						
					break;
					
					case ($seconds > 60 && $seconds < 3600) :
					
						$seconds = round($seconds / 60);
					
						if($seconds == 1  || $seconds == 21 || $seconds == 31 || $seconds == 41 || $seconds == 51)
						{
							$time = $seconds . ' minute ago.';
						}
						else
						{
							$time = $seconds . ' minutes ago.';
						}
					break;
					
					case ($seconds > 3600 && $seconds < 86400) :
						
						$seconds = round($seconds / 3600);
					
						if($seconds == 1  || $seconds == 21)
						{
							$time = $seconds . ' hour ago.';
						}
						else
						{
							$time = $seconds . ' hours ago.';
						}
						
					break;
					
					case ($seconds > 86400 && $seconds < 172800) :
						$time = 'Yeterday.';
					break;
					
					case $seconds > 172800:
						$time = 'on '.date("d M Y H:i", $timestamp);
					break;
				}
			}
						
			return $time;
		}
	}
	
?>