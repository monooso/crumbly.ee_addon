<?=form_open($form_action); ?>
<div id="crumbly">

<table class="mainTable padTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_url_segment'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
	<?php
		if ( ! $settings['glossary']):
	?>
		<tr class="row">
			<td><input type="text" name="glossary[0][url_segment]"></td>
			<td><input type="text" name="glossary[0][display_title]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
	<?php
		else:
		foreach ($settings['glossary'] AS $glossary_item):
	?>
		<tr class="row">
			<td><input type="text" name="glossary[0][url_segment]" value="<?=$glossary_item->get_glossary_term(); ?>"></td>
			<td><input type="text" name="glossary[0][display_title]" value="<?=$glossary_item->get_glossary_definition(); ?>"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
	<?php
		endforeach;
		endif;
	?>
	</tbody>
</table>

</div><!-- /#crumbly -->

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_glossary'), 'class' => 'submit')); ?></div>

<?=form_close(); ?>
