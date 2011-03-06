/**
 * Crumbly control panel behaviours.
 *
 * @author			Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright		Experience Internet
 * @package			Crumbly
 */

(function($) {

	/**
	 * Updates the glossary field indexes.
	 *
	 * @access	private
	 */
	function updateGlossaryIndex() {
		$('#crumbly_glossary tbody tr').each(function(rowCount) {

			$(this).find('input[type="text"]').each(function(fieldCount) {
				fieldName	= $(this).attr('name');
				regex		= /^([a-z_]+)\[(?:[0-9]+)\]\[([a-z_]+)\]$/;
				fieldName	= fieldName.replace(regex, '$1[' + rowCount + '][$2]');

				$(this).attr('name', fieldName);
			});

		});
	}


	/**
	 * If the supplied jQuery object is a table with a single tbody row, applies a
	 * class of 'only_row' to the tr. Otherwise, removes any 'only_row' classes.
	 *
	 * @access	private
	 */
	function updateOnlyRowIndicator($table) {
		$table.find('tbody tr').removeClass('only_row');

		if ($table.find('tbody tr').size() == 1) {
			$table.find('tbody tr:first').addClass('only_row');
		}
	}



	$(document).ready(function() {

		updateOnlyRowIndicator($('#crumbly_glossary'));

		// Add Row.
		$('.add_row').live('click', function() {
			$tr		= $(this).closest('tr');
			$table	= $(this).closest('table');
			$clone	= $tr.clone();

			$clone.find('input[type="text"]').val('');
			$tr.after($clone);

			updateGlossaryIndex();
			updateOnlyRowIndicator($table);

			return false;
		});


		// Delete Row.
		$('.delete_row').live('click', function() {
			$tr		= $(this).closest('tr');
			$table	= $(this).closest('table');

			// Can't remove the only row.
			if ($tr.siblings().length > 0) {
				$tr.remove();
			}

			updateGlossaryIndex();
			updateOnlyRowIndicator($table);

			return false;
		});

	});

})(jQuery)

/* End of file		: cp.css */
/* File location	: themes/third_party/crumbly/css/cp.css */
