/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */

define([
    'jquery',
    'Buzzi_Publish/js/model/storage'
], function ($, buzziStorage) {
    "use strict";

    if (!buzziStorage.has('collectGuests')) {
        return;
    }

    var collectGuests = buzziStorage.get('collectGuests') ? 1 : 0;

    $('<input />').attr('type', 'hidden')
        .attr('name', "accepts_marketing")
        .attr('value', collectGuests)
        .appendTo('form.form-create-account');
});
