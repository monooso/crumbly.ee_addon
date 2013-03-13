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
    if ( ! $glossary):
  ?>
    <tr class="row">
      <td><input type="text" name="glossary[0][glossary_term]"></td>
      <td><input type="text" name="glossary[0][glossary_definition]"></td>
      <td class="act">
        <a class="remove_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/minus.png" width="16"></a>
        <a class="add_row btn" href="#"><img height="17" src="<?php echo URL_THIRD_THEMES; ?>crumbly/img/plus.png" width="16"></a>
      </td>
    </tr>
  <?php
    else:
    foreach ($glossary AS $glossary_item):
  ?>
    <tr class="row">
      <td><input type="text" name="glossary[0][glossary_term]" value="<?=form_prep($glossary_item->get_glossary_term()); ?>"></td>
            <td><input type="text" name="glossary[0][glossary_definition]" value="<?=form_prep($glossary_item->get_glossary_definition()); ?>"></td>
      <td class="act">
        <a class="remove_row btn" href="#"><img height="17" src="<?php echo PURL_THIRD_THEMES; ?>crumbly/img/minus.png" width="16"></a>
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

<p><?=form_submit(array('name' => 'submit', 'value' => lang('lbl_save_glossary'), 'class' => 'submit')); ?></p>

<?=form_close(); ?>
