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
;(function ( window, document ) {

    function Superdropdown(options) {

        if (typeof options.id === "string") {
            this.element = document.getElementById(options.id);
        } else {
            this.element = options.element;
        }
        this.options = options;

        this.init();
    }

    Superdropdown.prototype = {

        selects: [],
        activeChildofSelect: {},

        init() {

            const _self = this;

            this.selects = this.element.querySelectorAll("select");

            this.selects.forEach(function(select) {

                select.addEventListener('change', _self.handleChange.bind(_self) );

                // set initial values
                const value = select.getAttribute('data-initialvalue');
                select.selectedIndex = parseInt(value);

                // set up initial select states
                const selectedOption = select.selectedOptions[0];
                if (selectedOption) {
                    _self.showSelect(select, selectedOption);
                }

            });

            console.log('find me');

        },

        handleChange(e) {

            const select = e.target;
            const selectedOption = select.selectedOptions[0];

            this.removeChildSelect(select);

            this.showSelect(select, selectedOption);


        },

        showSelect(select, selectedOption) {

            const target = selectedOption.getAttribute('data-target');

            // show target dropdown & set its first option as selected
            if (target !== null) {

                const childSelect = document.getElementById(target);
                childSelect.closest('.sd-selectWrap').classList.add("isActive");
                // set the first option as selected if there is no selection
                if (childSelect.selectedIndex === -1) {
                    childSelect.selectedIndex = 0;
                }

                // register child select on this select
                this.activeChildofSelect[select.id] = childSelect;

                // show children
                const childSelectedOption = childSelect.selectedOptions[0];
                this.showSelect(childSelect, childSelectedOption);

            }
        },

        removeSelect(select) {
            select.closest('.sd-selectWrap').classList.remove("isActive");
            select.selectedIndex = -1;

            this.removeChildSelect(select);

        },

        removeChildSelect(select) {
            // if a child select is registered on this select, then remove it
            if (this.activeChildofSelect.hasOwnProperty(select.id))  {
                const childSelect = this.activeChildofSelect[select.id];
                // remove child from array
                delete this.activeChildofSelect[select.id];
                this.removeSelect(childSelect);
            }
        },

        getInputValue() {

            let value = [];
            this.element.querySelectorAll("select.isActive").forEach(function(dropdown) {
                const strParts = dropdown.getAttribute('id').split(/\[(.*?)\]/);
                const key = strParts[strParts.length-2];

                value[key] = dropdown.selectedOptions[0].value;
            });

            return value;

        }

    };

    window.CE_Superdropdown = function( options ) {
        return new Superdropdown( options )
    }

})(window, document);

// const dropdown = window.CE_Superdropdown(options);
// const value = dropdown.getInputValue();

