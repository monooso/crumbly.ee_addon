<div id="crumbly">
<?=form_open($form_action); ?>

<table cellpadding="0" cellspacing="0" id="crumbly_glossary">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_url_segment'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody>
	<?php
		$count = 0;
		foreach ($settings['glossary'] AS $key => $val):
	?>
		<tr>
			<td><input type="text" name="glossary[<?=$count; ?>][url_segment]" value="<?=$key; ?>"></td>
			<td><input type="text" name="glossary[<?=$count; ?>][display_title]" value="<?=$val; ?>"></td>
			<td class="act">
				<a class="delete_row btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add_row btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
	<?php
		$count++;
		endforeach;
	?>
	</tbody>
</table>

</div>

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_settings'), 'class' => 'submit')); ?></div>

<?=form_close(); ?>
