<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @prepare() ?>
<? if(@$subforums = @$subforum->getList()) : ?>
	<li>
		<span class="folder"><?= @template('link') ?></span>
		<? if (@$subforum->getTotal()>0) : ?>
			<ul>
				<? @$total = @$total + @$subforum->getTotal() ?>
				<? foreach (@$subforums as @$i => @$forum) : ?>
					<?= @template('links') ?>
				<? endforeach ?>
			</ul>
		<? endif ?>
	</li>
<? else : ?>
	<li>
		<span class="file"><?= @template('link') ?></span>
	</li>
<? endif ?>