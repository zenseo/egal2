/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

// Initialize the jQuery File Upload widget:

$(function () {
    'use strict';
    //localStorage.setItem('id1',(content_id));

    var $fileupload = $('#fileupload');
    var $settings = $('#settings');

    $fileupload.fileupload({
        //disableImageResize: false,
        autoUpload: true,
        url: 'server/php/index.php',
//        maxFileSize: 5000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        previewMaxWidth: 400, previewMaxHeight: 400,
//        data: $fileupload.serializeArray(),
        always: function (e, data) {
        },
        started: function () {
        },
        stopped: function () {
            sortIt();
        }

    });

    localStorage.getItem('id1') ? $("#pId").val(localStorage.getItem('id1')) : $("#pId").val(1);
    loadSettings();

    $settings.on("change", '#pId', function () {localStorage.setItem('id1',this.value);loadSettings();loadImg();});
    $("input#size").on("input", function () {$("ol li").css("width", this.value + "%")});
    $("button#list").on("click", function () {$("ol li").toggleClass("list")});


    select();
    loadImg();
    sort();

    //************* update settings
    $settings.on('change', '.settings', (function () {
        $fileupload.addClass('fileupload-processing');
        var data = $fileupload.serializeArray();
        data[data.length] = { name: "update", value: "1" };
        $.post('server/php/save.php', data, function (data) {
            $fileupload.removeClass('fileupload-processing');
        })
    }));

    //************* update details
    $fileupload.on('change', 'form.details input, form.details textarea', (function () {
        $fileupload.addClass('fileupload-processing');
        $.post('server/php/save.php', $(this).parent().serialize(), function (data) {
            $fileupload.removeClass('fileupload-processing');
        })
    }));

    //************* select doc from tree and show images
    function select() {

        top.tree.ca = 'move';
        top.main.setMoveValue = function selectId(pId, pName) {
            $fileupload.addClass('fileupload-processing');
            $("h1").html(pName);
            $("#pId").val(pId).change();
        }
    }

    function loadSettings() {
        var data = $fileupload.serializeArray();
        data[data.length] = { name: "select", value: "1" };
        $.post('server/php/save.php', data, function (data) {
            $.each($.parseJSON(data), function (i, val) {
                $("#settings [name=" + i + "]").val(val);
            });
        })
    }

    function loadImg() {
        $fileupload.addClass('fileupload-processing');
        $.ajax({
            url: $fileupload.fileupload('option', 'url'),
            dataType: 'json',
            data: $fileupload.serializeArray(),
            context: $fileupload[0]
        }).always(function (result) {
            $(this).removeClass('fileupload-processing');
            $('.template-download').remove();
        }).done(function (result) {
            $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});

            $('.files').magnificPopup({
                midClick: true,
                gallery: {enabled: true},
                image: {titleSrc: 'title'},
                delegate: 'a', // child items selector, by clicking on it popup will open
                type: 'image'});
        });
    }


    $(".regenerate").click(function (e) {
        $fileupload.addClass('fileupload-processing');
        e.preventDefault(e);
        var data=$fileupload.serializeArray();
        data[data.length] = { name: "regenerate", value: "1" };
        $.ajax({
            url: $fileupload.fileupload('option', 'url'),
            dataType: 'json',
            data: data,
            context: $fileupload[0]
        }).always(function (result) {
            $('.template-download').remove();
            $("#err").html(result.responseText);
        }).done(function (result) {
            $("#err").html(result.responseText);
            $(this).removeClass('fileupload-processing');
            $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
        });

    });

    function sort() {
        var sortable1 = document.getElementById("sortable1");
        new Sortable(sortable1, {
            handle: "span",
            onUpdate: function () { sortIt(); },
            ghostClass: "sortable-ghost"
        });
    }

    function sortIt() {
        $fileupload.addClass('fileupload-processing');
        for (var i = 0, len = $('#sortable1 li.template-download').length; i < len; i++) {
            $('#sortable1 li input[name=sortorder]')[i].value = i + 1;
        }

        var sorting = {};
        $('ol li form').each(function (i) {
            sorting['sort[' + i + '][id]'] = this[5].value;
            sorting['sort[' + i + '][sortorder]'] = this[4].value;
        })

        //console.log(e);

        $.post('server/php/save.php', sorting, function (data) {
            $fileupload.removeClass('fileupload-processing');
        })

    }


});
