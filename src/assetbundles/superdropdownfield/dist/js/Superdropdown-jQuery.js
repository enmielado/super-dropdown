/**
 * Super dropdown plugin for Craft CMS
 *
 * Superdropdown Field JS
 *
 * @author    veryfinework
 * @copyright Copyright (c) 2020 veryfinework
 * @link      https://github.com/veryfinework
 * @package   Superdropdown
 * @since     1.0.0
 */

 ;(function ( $, window, document, undefined ) {

    var pluginName = "SuperdropdownSuperdropdown",
        defaults = {
        };

    // Plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        this.options = $.extend( {}, defaults, options) ;

        this._defaults = defaults;
        this._name = pluginName;

        this.init();
    }

    Plugin.prototype = {

        chain: [],

        init: function() {

            console.log('find me');

            var _self = this;

            const selects = $("[id^="+_self.options.namespace+"] select");

            // fill the chain with the initial set of active dropdowns
            this.chain = $("select.isActive", this.element);

            selects.change( function() {

                const $select = $(this);
                const selectedOption = this.selectedOptions[0];
                let target = selectedOption.getAttribute('data-target');

                if ($select.is(_self.chain)) {

                    // get the index of this select in the chain
                    const index = _self.chain.index($select);

                    // remove active class from all items above this index & deselect dropdown option
                    const chainLength = _self.chain.length;
                    if (index < chainLength-1) {
                        for (let i = index; i < chainLength - 1; i++) {
                            // if isPrimary then skip
                            const chainSelect =  _self.chain[i + 1];
                            if (!chainSelect.classList.contains('type-primary')) {
                                chainSelect.classList.remove('isActive');
                                chainSelect.selectedIndex = -1;
                            }
                        }
                    }

                }

                // show target dropdown & set the first option in it as selected
                if (target !== null) {
                    const newDropdown = document.getElementById(target);
                    newDropdown.classList.add("isActive");
                    newDropdown.selectedIndex = 0;

                    $select.data('child', target);
                }

                // reset chain
                _self.chain = $("select.isActive", _self.element);

                const v = _self.getInputValue();

            });

        },

        getInputValue: function() {

            const activeDropdowns = this.element.querySelectorAll("select.isActive");

            let value = [];
            activeDropdowns.forEach(function(dropdown) {
                const strParts = dropdown.getAttribute('id').split(/\[(.*?)\]/);
                const key = strParts[strParts.length-2];

                value[key] = dropdown.selectedOptions[0].value;
            });

            return value;

        }

    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName,
                new Plugin( this, options ));
            }
        });
    };

})( jQuery, window, document );
