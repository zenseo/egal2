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

$(function () {
    'use strict';
    $(".regenerate").click(function (e) {
        e.preventDefault(e);
        var data=$fileupload.serializeArray();//+"&regenerate"//.push('regenerate');
        data[data.length] = { name: "regenerate", value: "1" };
        $fileupload.addClass('fileupload-processing');
        $.ajax({
            url: $fileupload.fileupload('option', 'url'),
            dataType: 'json',
            data: data,
            context: $fileupload[0]
        }).always(function (result) {
            $('.template-download').remove();
        }).done(function (result) {
            $(this).removeClass('fileupload-processing');
            $(this).fileupload('option', 'done').call(this, $.Event('done'), {result: result});
            //$("#sortable1").slideDown(300);
            //sortIt();

//            $('.files').magnificPopup({
//                gallery: {enabled: true},
//                image: {titleSrc: 'title'},
//                delegate: 'a', // child items selector, by clicking on it popup will open
//                type: 'image'});
        });

    });

    $("#sortable1").on("click", ".preview", function () {
        $(this).siblings('.toggle').click();
    });
    var $fileupload = $('#fileupload');
    var $settings = $('#settings');
    $("#pId").val(localStorage.getItem('id1'))
        .on("change", function () {
            localStorage.setItem('id1', this.value);
            loadImg();
        });

//    $("html").dblclick( function(){
//        $(".fileinput-button input").click()
//    });

    $("input#size").on("input", function () {
//        alert (this.value);
        $("ol li").css("width", this.value + "%")
    });
    $("button#list").on("click", function () {
        $("ol li").toggleClass("list")

    });


    function loadImg() {
        $fileupload.addClass('fileupload-processing');
        //$("#sortable1").hide();
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
            //$("#sortable1").slideDown(300);
            //sortIt();

            $('.files').magnificPopup({
                midClick: true,
                gallery: {enabled: true},
                image: {titleSrc: 'title'},
                delegate: 'a', // child items selector, by clicking on it popup will open
                type: 'image'});
        });
    }

    //************* select doc from tree and show images
    function select() {

        top.tree.ca = 'move';
        top.main.setMoveValue = function selectId(pId, pName) {
            $fileupload.addClass('fileupload-processing');
            $("h1").html(pName);
            $("#pId").val(pId);

var data=$fileupload.serializeArray();
data[data.length] = { name: "select", value: "1" };
            $.post('server/php/save.php', data, function (data) {
               
               //console.log($.parseJSON(data));
               //console.log((data));
                $.each($.parseJSON(data), function (i, val) {
                    $("#settings [name=" + i + "]").val(val);
                });

                localStorage.setItem('id1', pId);

                loadImg();
            });

            localStorage.setItem('id1', pId);
            //loadImg();
        }
    }


    // Initialize the jQuery File Upload widget:
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
    });//.on('fileuploadsubmit', function (e, data) {    });


    select();
    loadImg();
    sort();


//************* update settings
    $settings.on('change', 'input, select', (function () {
        $fileupload.addClass('fileupload-processing');
        var data=$fileupload.serializeArray();
        data[data.length] = { name: "update", value: "1" };
        $.post('server/php/save.php', data, function (data) {
            $fileupload.removeClass('fileupload-processing');
            //loadImg()
        })
    }));

//************* update details
    $fileupload.on('change', 'form.details input, form.details textarea', (function () {
        $fileupload.addClass('fileupload-processing');
        $.post('server/php/save.php', $(this).parent().serialize(), function (data) {
            $fileupload.removeClass('fileupload-processing');
        })
    }));


    function sort() {
        $fileupload.addClass('fileupload-processing');

        var sortable1 = document.getElementById("sortable1");
        new Sortable(sortable1, {
            handle: "span",
            onUpdate: function () {
                sortIt();
            },
//            onAdd: function () {
//                sortIt();
//            },
//            onRemove: function () {
//                sortIt();
//            },
            ghostClass: "sortable-ghost"
//            group: "sortable1"
            //draggable: ".template-download",
        });

        //sortIt();

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


    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );


});