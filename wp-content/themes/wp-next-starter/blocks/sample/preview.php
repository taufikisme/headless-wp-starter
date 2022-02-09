<?php get_template_part( 'template-parts/components/block-preview', null, array(
	'block_name'	=> 'Sample',
	'fields'		=> [
		[
			'field_label'	=> 'Name',
			'field_type'	=> 'text',
			'value'			=> block_value('name')
		],
		[
			'field_label'	=> 'Email',
			'field_type'	=> 'text',
			'value'			=> block_value('email')
		],
		[
			'field_label'	=> 'Messages',
			'field_type'	=> 'textarea',
			'value'			=> block_value('messages')
		]
	]
) ); ?>