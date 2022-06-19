/*!
 * REVOLUTION 6.0.0 EDITOR HELPINIT JS
 * @version: 1.0 (01.07.2019)
 * @author ThemePunch
*/

// addons extension
;window.HelpGuide = {

	addOnsHelp: [],

	/* verify directory entry is an object */
	verifyObject: function(obj) {

		return obj && typeof obj === 'object' && !Array.isArray(obj);

	},
	/* called from an AddOn, adds the help data */
	add: function(obj) {

		if(HelpGuide.verifyObject(obj)) {

			/* if help not activated yet, store the data */
			if(!HelpGuide.allHelpPaths) {

				HelpGuide.addOnsHelp[HelpGuide.addOnsHelp.length] = obj;

			}
			/* if help activated, add the data */
			else {

				HelpGuide.extendHelpAddOns([obj], true);

			}

		}

	},
	/* called from an AddOn, show help info if help is activated and AddOn is active */
	activate: function(slug) {

		if(HelpGuide.allHelpPaths) HelpGuide.toggleHelpAddOn(slug, true);


	},
	/* called from an AddOn, hide help info if help is activated and AddOn is not active */
	deactivate: function(slug) {

		if(HelpGuide.allHelpPaths) HelpGuide.toggleHelpAddOn(slug);

	}

};

// load main script when help guide is first clicked
jQuery(function() {

	/*
		HELP DIRECTORY
	*/
	jQuery('.help_wrap').one('click', function(e) {
		require(['help']);
	});

	/*
		TOOLTIPS
	*/
	var tooltipLoaded;
	jQuery('.tooltip_wrap').on('click', function() {

		var $this = jQuery(this),
			scriptReady = $this.data('scriptready');

		if(tooltipLoaded && !scriptReady) return;
		if(!tooltipLoaded) require(['tooltip']);
		else jQuery(document).trigger('start-tooltips');

		tooltipLoaded = true;

	});

	window.RsTooltips = function(activate, list, definitions) {

		window.RsTooltipList = list;
		if(revSliderToolTips && activate) {

			jQuery('.tooltip_wrap').data('tooltip-definitions', definitions).trigger('click');

		}

	};

});
