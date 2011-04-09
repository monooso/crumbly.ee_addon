<?php if ( ! $categories_dd): ?>
<div id="crumbly">
<div class="no_content"><p>There are no categories for this site.</p></div>
</div><!-- /#crumbly -->

<?php else: ?>
<?=form_open($form_action); ?>
<div id="crumbly">
<table class="mainTable padTable" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="48%"><?=lang('thd_category'); ?></th>
			<th><?=lang('thd_display_title'); ?></th>
			<th>&nbsp;</th>
		</tr>
	</thead>

	<tbody class="roland">
		<?php if ( ! $categories): ?>
		<tr class="row">
			<td><?=form_dropdown('categories[0][cat_id]', $categories_dd); ?></td>
			<td><input type="text" name="categories[0][label]"></td>
			<td class="act">
				<a class="remove btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/minus.png" width="16"></a>
				<a class="add btn" href="#"><img height="17" src="/themes/third_party/crumbly/img/plus.png" width="16"></a>
			</td>
		</tr>
		<?php
			else:
			foreach ($categories AS $category):
		?>
			<tr class="row">
				<td><?=form_dropdown('categories[0][cat_id]', $categories_dd, $category->get_cat_id()); ?></td>
				<td><input type="text" name="categories[0][label]" value="<?=$category->get_label(); ?>"></td>
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

<div class="submit_wrapper"><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_categories'), 'class' => 'submit')); ?></div>
<?=form_close(); ?>
<?php endif; ?>
