<?=form_open($form_action); ?>
<div id="crumbly">

<table class="mainTable padTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_template'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
		<?php if ( ! $templates): ?>
		<tr class="row">
			<td><?=form_dropdown('templates[0][template_id]', $templates_dd); ?></td>
			<td><input type="text" name="templates[0][label]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
		<?php
			else:
			foreach ($templates AS $template):
		?>
			<tr class="row">
				<td><?=form_dropdown('templates[0][template_id]', $templates_dd, $template->get_template_id()); ?></td>
				<td><input type="text" name="templates[0][label]" value="<?=$template->get_label(); ?>"></td>
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

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_templates'), 'class' => 'submit')); ?></div>

<?=form_close(); ?>
