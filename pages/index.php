<?php

	namespace drivers;
	namespace applications;
	
		use drivers as driver;
		use applications as app;

	$username = 'Voomes';
	$oldtime = '1407195620';

	$template = new driver\template;
	$time = new app\time;

	$template->set('index');

	$template->username = $username;
	$template->timetext = $time->convert($oldtimestamp);
		
	$template->render();
		
	echo $template->html;		

/* End */	

?>