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
        <a class="remove_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/minus.png" width="16"></a>
        <a class="add_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/plus.png" width="16"></a>
      </td>
    </tr>
    <?php
      else:
      foreach ($templates AS $template):
    ?>
      <tr class="row">
        <td><?=form_dropdown('templates[0][template_id]', $templates_dd, $template->get_template_id()); ?></td>
        <td><input type="text" name="templates[0][label]" value="<?=form_prep($template->get_label()); ?>"></td>
        <td class="act">
          <a class="remove_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/minus.png" width="16"></a>
          <a class="add_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/plus.png" width="16"></a>
        </td>
      </tr>
    <?php
      endforeach;
      endif;
    ?>
  </tbody>
</table>

</div><!-- /#crumbly -->

<p><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_templates'), 'class' => 'submit')); ?></p>

<?=form_close(); ?>
