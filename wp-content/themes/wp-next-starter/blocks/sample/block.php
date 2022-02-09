<?php

$json_data = array(
		'block_name' => 'sample',
		'primary' => array(
			'name'	=> block_value('name'),
			'email'	=> block_value('email'),
			'messages'	=> block_value('messages')
		),
		'items' => block_value('sample-repeater')
	);

$myJSON = json_encode($json_data);

echo $myJSON.',';

?>