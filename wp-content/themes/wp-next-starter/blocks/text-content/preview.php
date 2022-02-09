<?php get_template_part( 'template-parts/components/block-preview', null, array(
	'block_name'	=> 'Text Content',
	'fields'		=> [
		[
			'field_label'	=> 'Name',
			'field_type'	=> 'text',
			'value'			=> block_value('name')
		]
	]
) ); ?>