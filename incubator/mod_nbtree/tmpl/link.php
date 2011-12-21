<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>
	d<?= @$module->id?>.add('<?= (@$forum->id=='') ? '0' : @$forum->id; ?>', 
                            '<?= @$parent_id?>', 
                            '<?= (@escape(@$forum->title)=='') ? 'Forums Index' : @escape(@$forum->title); ?>',
                            '<?= (@$forum->id=='') ? @route('option=com_ninjaboard&view=forums') : @route('option=com_ninjaboard&view=forum&id='.@$forum->id); ?>', 
                            '<?= (@escape(@$forum->title)=='') ? 'Forums Index' : @escape(@$forum->title); ?>', 
                            '', 
                            '<?= @$Icon?>',
                            '<?= @$IconOpen?>'
                            );