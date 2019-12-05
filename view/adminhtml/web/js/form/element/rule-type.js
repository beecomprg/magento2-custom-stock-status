define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(id) {
            let select =  $("#" + id);
            let container = select.closest('tr');
            if ((select.val() == 5)) {
                container.addClass('showInputs');
            } else {
                container.removeClass('showInputs');
            }
        },
    });
});
