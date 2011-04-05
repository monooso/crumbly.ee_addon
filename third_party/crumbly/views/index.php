<?=form_open($form_action); ?>
<div id="crumbly">

<h3><?=lang('hd_glossary'); ?></h3>
<table cellpadding="0" cellspacing="0" id="crumbly_glossary">
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

<h3><?=lang('hd_template_groups'); ?></h3>
<table cellpadding="0" cellspacing="0" id="crumbly_glossary">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_template_group'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
	<?php if ( ! $settings['template_groups']): ?>
		<tr class="row">
			<td><?=form_dropdown('template_groups[0][group_id]', $template_groups); ?></td>
			<td><input type="text" name="template_groups[0][label]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
	<?php
		else:
		foreach ($settings['template_groups'] AS $crumbly_template_group):
	?>
		<tr class="row">
			<td><?=form_dropdown('template_groups[0][group_id]', $template_groups, $crumbly_template_group->get_group_id()); ?></td>
			<td><input type="text" name="template_groups[0][label]" value="<?=$crumbly_template_group->get_label(); ?>"></td>
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

<h3><?=lang('hd_templates'); ?></h3>
<table cellpadding="0" cellspacing="0" id="crumbly_glossary">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_template'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
		<?php if ( ! $settings['templates']): ?>
		<tr class="row">
			<td><?=form_dropdown('templates[0][template_id]', $templates); ?></td>
			<td><input type="text" name="templates[0][label]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
		<?php
			else:
			foreach ($settings['templates'] AS $crumbly_template):
		?>
			<tr class="row">
				<td><?=form_dropdown('templates[0][template_id]', $templates, $crumbly_template->get_template_id()); ?></td>
				<td><input type="text" name="templates[0][label]" value="<?=$crumbly_template->get_label(); ?>"></td>
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

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_settings'), 'class' => 'submit')); ?></div>

<?=form_close(); ?>
