/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery',
    'Buzzi_Publish/js/model/storage',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/lib/validation/validator',
    'Buzzi_Publish/js/action/send-saved-events'
], function ($, buzziStorage, customerData, validator, sendSavedEvents) {
    'use strict';

    if (customerData.get('customer')().firstname) {
        sendSavedEvents();
        return;
    }

    if (buzziStorage.has('guestEmail')) {
        sendSavedEvents();
    }

    $('body').on('change', "input[type='email'],input[data-validate~='validate-email']", function() {
        var emailValue = $(this).val();
        if (validator('validate-email', emailValue).passed) {
            buzziStorage.set('guestEmail', emailValue);
            sendSavedEvents();
        }
    });
});
