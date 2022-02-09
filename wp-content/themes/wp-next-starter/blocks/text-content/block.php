<?php

$json_data = array(
		'block_name' => 'text_content', // use snake format
		'primary' => array(
			'name'	=> block_value('name')
		),
		'items' => ''
	);

$myJSON = json_encode($json_data);

echo $myJSON.',';

?>