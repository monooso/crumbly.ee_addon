/**
 * Add and delete 'rows' to any container element.
 *
 * @author			Stephen Lewis
 * @package			Geek Hunter
 */

(function($) {

	function updateIndexes($container) {
		$container.find($container.data('roland').settings.rowIdentifier).each(function(rowCount) {
			regex = /^([a-z_]+)\[(?:[0-9]+)\]\[([a-z_]+)\]$/;

			$(this).find('input, select, textarea').each(function(fieldCount) {
				fieldName	= $(this).attr('name');
				fieldName	= fieldName.replace(regex, '$1[' + rowCount + '][$2]');
				$(this).attr('name', fieldName);
			});

		});
	};

	function updateNav($container) {
		$remove = $container.find('.' + $container.data('roland').settings.removeClass);
		$rows	= $container.find($container.data('roland').settings.rowIdentifier);
		
		$remove.show();

		if ($rows.size() == 1) {
			$remove.hide();
		}
	};

	var publicMethods = {
		ini : function(settings) {
			return this.each(function() {
				$container = $(this);
				$container.data('roland', {'settings' : settings});

				$container
					.find('.' + settings.addClass)
					.data('roland', $.extend({}, settings, {'container' : $container}))
					.bind('click.roland', publicMethods.addRow);

				$container
					.find('.' + settings.removeClass)
					.bind('click.roland', publicMethods.removeRow)
					.data('roland', $.extend({}, settings, {'container' : $container}));

				updateIndexes($container);
				updateNav($container);
			});
		},

		addRow : function() {
			$container	= $(this).data('roland').container;
			rowIdentifier = $container.data('roland').settings.rowIdentifier;
			$parentRow	= $(this).closest(rowIdentifier);
			$lastRow	= $container.find(rowIdentifier + ':last');
			$cloneRow	= $lastRow.clone(true);

			$cloneRow.find('input').each(function() {
				type = $(this).attr('type');

				switch (type.toLowerCase()) {
					case 'checkbox':
					case 'radio':
						$(this).attr('checked', false);
						break;

					case 'password':
					case 'search':
					case 'text':
						$(this).val('');
						break;
				}
			});

			// Add the new row.
			typeof $parentRow === 'object' ? $parentRow.after($cloneRow) : $lastRow.append($cloneRow);
			
			updateIndexes($container);
			updateNav($container);

			return false;
		},

		removeRow : function() {
			$container	= $(this).data('roland').container;
			$row		= $(this).closest($container.data('roland').settings.rowIdentifier);

			if ($row.siblings().length == 0) {
				return false;
			}

			$row.remove();

			updateIndexes($container);
			updateNav($container);

			return false;
		},

	};

	$.fn.roland = function(method) {

		// Default options.
		var defaultSettings = {
			'addClass'		: 'add',
			'removeClass'	: 'remove',
			'rowIdentifier'	: '.row'
		};

		if (publicMethods[method]) {
			return publicMethods[method].apply(this, Array.prototype.slice.call(arguments, 1));

		} else if (typeof method === 'object' || ! method) {
			if (typeof method === 'object') {
				$.extend(defaultSettings, method);
			}

			return publicMethods.ini.call(this, defaultSettings);

		} else {
			$.error('Method ' + method + ' does not exist in jQuery.roland');
		}

	};

})(jQuery);
