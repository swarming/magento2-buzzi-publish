/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'Buzzi_Publish/js/model/storage',
    'Magento_Customer/js/customer-data'
], function (buzziStorage, customerData) {
    "use strict";

    return {
        isCustomerLoggedIn: function () {
            return !!customerData.get('customer')().firstname;
        },

        isAllowCollectGuestData: function () {
            return buzziStorage.has('collectGuests')
                ? buzziStorage.get('collectGuests')
                : window.buzzi.collect_guest_data;
        },

        getMaxGuestEvents: function () {
            return window.buzzi.max_guest_events;
        }
    };
});
