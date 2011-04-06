<?=form_open($form_action); ?>
<div id="crumbly">

<table class="mainTable padTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_template_group'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
	<?php if ( ! $template_groups): ?>
		<tr class="row">
			<td><?=form_dropdown('template_groups[0][group_id]', $template_groups_dd); ?></td>
			<td><input type="text" name="template_groups[0][label]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
	<?php
		else:
		foreach ($template_groups AS $template_group):
	?>
		<tr class="row">
			<td><?=form_dropdown('template_groups[0][group_id]', $template_groups_dd, $template_group->get_group_id()); ?></td>
			<td><input type="text" name="template_groups[0][label]" value="<?=$template_group->get_label(); ?>"></td>
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

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_template_groups'), 'class' => 'submit')); ?></div>

<?=form_close(); ?>
