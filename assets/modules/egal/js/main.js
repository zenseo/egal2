/*
 * jQuery File Upload Plugin JS Example 8.8.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, regexp: true */
/*global $, window, blueimp */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({//dropZone: $('#dropzone'),
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'server/php/'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
                $('.files').magnificPopup({delegate: 'a',type:'image'});
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, null, {result: result});
                $('.files').magnificPopup({delegate: 'a',type:'image'});
        });


});





//$(document).bind('dragover', function (e) {
//    var dropZone = $('#dropzone'),
//        timeout = window.dropZoneTimeout;
//    if (!timeout) {
//        dropZone.addClass('in');
//    } else {
//        clearTimeout(timeout);
//    }
//    var found = false,
//        node = e.target;
//    do {
//        if (node === dropZone[0]) {
//            found = true;
//            break;
//        }
//        node = node.parentNode;
//    } while (node != null);
//    if (found) {
//        dropZone.addClass('hover');
//    } else {
//        dropZone.removeClass('hover');
//    }
//    window.dropZoneTimeout = setTimeout(function () {
//        window.dropZoneTimeout = null;
//        dropZone.removeClass('in hover');
//    }, 100);
//});
//
//$(document).bind('drop dragover', function (e) {
//    e.preventDefault();
//});