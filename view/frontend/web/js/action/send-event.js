/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery',
    'mage/storage',
    'Magento_Customer/js/customer-data',
    'Buzzi_Publish/js/model/config',
    'Buzzi_Publish/js/action/save-event',
    'Buzzi_Publish/js/action/guest-email'
], function ($, storage, customerData, buzziConfig, saveEvent, guestEmail) {
    "use strict";

    return function (eventType, inputData, uniqueKey) {
        var isLoggedIn = customerData.get('customer')().firstname;

        var email = guestEmail();
        var url = isLoggedIn ? '/rest/V1/buzzi/mine/publish-event' : '/rest/V1/buzzi/guest/publish-event';
        if (!isLoggedIn && !email) {
            saveEvent(eventType, inputData, uniqueKey);
            return;
        }

        if (isLoggedIn && !buzziConfig.isExceptsMarketing()) {
            return;
        }

        var eventData = {
            eventType: eventType,
            inputData: inputData,
            guestEmail: email
        };
        return storage.post(url, JSON.stringify(eventData), false);
    };
});
