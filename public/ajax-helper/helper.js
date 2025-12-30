(function ($) {
    'use strict';
    $.easyAjax = function (options) {
        var defaults = {
            type: 'GET',
            container: 'body',
            blockUI: true,
            disableButton: false,
            buttonSelector: "[type='submit']",
            dataType: "json",
            messagePosition: "toastr",
            errorPosition: "field",
            hideElements: false,
            redirect: true,
            data: {},
            file: false,
            formReset: false
        };

        var opt = defaults;

        // Extend user-set options over defaults
        if (options) {
            opt = $.extend(defaults, options);
        }

        // Methods if not given in option
        if (typeof opt.beforeSend != "function") {
            opt.beforeSend = function () {
                // Hide previous errors
                $(opt.container).find(".has-error").each(function () {
                    $(this).find(".help-block").text("");
                    $(this).removeClass("has-error");
                });

                $(opt.container).find("#alert").html("");

                if (opt.blockUI) {
                    $.easyBlockUI(opt.container);
                }

                if (opt.disableButton) {
                    loadingButton(opt.buttonSelector);
                }
            }
        }

        if (typeof opt.complete != "function") {
            opt.complete = function (jqXHR, textStatus) {
                if (opt.blockUI) {
                    $.easyUnblockUI(opt.container);
                }

                if (opt.disableButton) {
                    unloadingButton(opt.buttonSelector)
                }
            }
        }

        // Default error handler
        if (typeof opt.error != "function") {
            opt.error = function (jqXHR, textStatus, errorThrown) {
                try {
                    var response = JSON.parse(jqXHR.responseText);
                    if (typeof response == "object") {
                        handleFail(response);
                    } else {
                        var msg = "A server side error occurred. Please try again after sometime.";

                        if (textStatus == "timeout") {
                            msg = "Connection timed out! Please check your internet connection";
                        }
                        showResponseMessage(msg, "error");
                    }
                } catch (e) {

                }
            }
        }

        function showResponseMessage(msg, type, toastrOptions) {
            var typeClasses = {
                "error": "danger",
                "success": "success",
                "primary": "primary",
                "warning": "warning",
                "info": "info"
            };

            var iconClasses = {
                "error": "error",
                "success": "success",
                "warning": "warning",
                "info": "info"
            };

            var headingClasses = {
                "error": "",
                "success": "",
                "warning": "",
                "info": ""
            };

            if (opt.messagePosition == "toastr") {
                //$.showToastr(msg, type, toastrOptions);
                $.toast({
                    heading: headingClasses[type],
                    text: msg,
                    position: 'top-right',
                    loaderBg: '#ff6849',
                    icon: iconClasses[type],
                    hideAfter: 3500

                });
            } else {
                var ele = $(opt.container).find("#alert");
                var html = '<div class="alert alert-' + typeClasses[type] + '">' + msg + '</div>';
                if (ele.length == 0) {
                    $(opt.container).find(".form-group:first")
                            .before('<div id="alert">' + html + "</div>");
                } else {
                    ele.html(html);
                }
            }
        }

        // Execute ajax request

        if (opt.file == true) {
            var data = new FormData($(opt.container)[0]);
            var keys = Object.keys(opt.data);

            for (var i = 0; i < keys.length; i++) {
                data.append(keys[i], opt.data[keys[i]]);
            }

            opt.data = data;
        }

        $.ajax({
            type: opt.type,
            url: opt.url,
            dataType: opt.dataType,
            data: opt.data,
            beforeSend: opt.beforeSend,
            contentType: (opt.file) ? false : "application/x-www-form-urlencoded; charset=UTF-8",
            processData: !opt.file,
            error: opt.error,
            complete: opt.complete,
            success: function (response) {
                // Show success message
                if (response.status == "success") {
                    if (response.action == "redirect") {
                        if (opt.redirect) {
                            var message = "";
                            if (typeof response.message != "undefined") {
                                message += response.message;
                            }
                            message += " Redirecting...";

                            showResponseMessage(message, "success", {
                                timeOut: 100000,
                                positionClass: "toast-top-right"
                            });
                            window.location.href = response.url;
                        }
                    } else {
                        if (typeof response.message != "undefined") {
                            showResponseMessage(response.message, "success");
                        }
                    }

                    if (opt.removeElements == true) {
                        $(opt.container).find(".form-group, button, input").remove();
                    }

                    if (opt.formReset == true) {
                        $(opt.container)[0].reset();
                    }
                }

                if (response.status == "fail") {
                    handleFail(response);
                }

                if (typeof opt.success == "function") {
                    opt.success(response);
                }
            }
        });

        function handleFail(response) {
            
            if (typeof response.message != "undefined") {
                showResponseMessage(response.message, "error");
            }

            if (typeof response.errors != "undefined") {
                var keys = Object.keys(response.errors);

                $(opt.container).find(".has-error").find(".help-block").remove();
                $(opt.container).find(".has-error").removeClass("has-error");

                if (opt.errorPosition == "field") {
                    for (var i = 0; i < keys.length; i++) {
                        // Escape dot that comes with error in array fields
                        var key = keys[i].replace(".", '\\.');
                        var formarray = keys[i];
                        // If the response has form array
                        if (formarray.indexOf('.') > 0) {
                            var array = formarray.split('.');
                            response.errors[keys[i]] = response.errors[keys[i]];
                            key = array[0] + '[]';
                        }

                        var ele = $(opt.container).find("[name='" + key + "']");

                        // If cannot find by name, then find by id
                        if (ele.length == 0) {
                            ele = $(opt.container).find("#" + key);
                        }

                        var grp = ele.closest("div");
                        $(grp).find(".help-block").remove();

                        //check if wysihtml5 editor exist
                        var wys = $(grp).find(".wysihtml5-toolbar").length;

                        if (wys > 0) {
                            var helpBlockContainer = $(grp);
                        } else {
                            var helpBlockContainer = $(grp).find("div:first");
                            if (helpBlockContainer.hasClass('input-group')) {
                                helpBlockContainer = $(grp);
                            }
                        }
                        if ($(ele).is(':radio')) {
                            helpBlockContainer = $(grp).find("div:eq(2)");
                        }

                        if (helpBlockContainer.length == 0) {
                            helpBlockContainer = $(grp);
                        }

                        helpBlockContainer.append('<div class="help-block">' + response.errors[keys[i]] + '</div>');
                        $(grp).addClass("has-error");
                    }

                    if (keys.length > 0) {
                        var element = $("[name='" + keys[0] + "']");
                        if (element.length > 0) {
                            $("html, body").animate({scrollTop: element.offset().top - 150}, 200);
                        }
                    }
                } else {
                    var errorMsg = "<ul>";
                    for (var i = 0; i < keys.length; i++) {
                        errorMsg += "<li>" + response.errors[keys[i]] + "</li>";
                    }
                    errorMsg += "</ul>";

                    var errorElement = $(opt.container).find("#alert");
                    var html = '<div class="alert alert-danger">' + errorMsg + '</div>';
                    if (errorElement.length == 0) {
                        $(opt.container).find(".form-group:first")
                                .before('<div id="alert">' + html + "</div>");
                    } else {
                        errorElement.html(html);
                    }
                }
            }
            if (response.action == "redirect") {
                if (opt.redirect) {
                    var message = "";
                    if (typeof response.message != "undefined") {
                        message += response.message;
                    }
                    message += " Redirecting...";


                    window.location.href = response.url;
                }
            }
        }

        function loadingButton(selector) {
            var button = $(opt.container).find(selector);

            var text = "Submitting...";

            if (button.width() < 20) {
                text = "...";
            }

            if (!button.is("input")) {
                button.attr("data-prev-text", button.html());
                button.text(text);
                button.prop("disabled", true);
            } else {
                button.attr("data-prev-text", button.val());
                button.val(text);
                button.prop("disabled", true);
            }
        }

        function unloadingButton(selector) {
            var button = $(opt.container).find(selector);

            if (!button.is("input")) {
                button.html(button.attr("data-prev-text"));
                button.prop("disabled", false);
            } else {
                button.val(button.attr("data-prev-text"));
                button.prop("disabled", false);
            }
        }
    };

    $.easyBlockUI = function (container, message) {
        if (message == undefined) {
            message = "Loading...";
        }

        var html = '<div class="loading-message"><div class="block-spinner-bar"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div></div>';


        if (container != undefined) { // element blocking
            var el = $(container);
            var centerY = false;
            if (el.height() <= ($(window).height())) {
                centerY = true;
            }
            el.block({
                message: html,
                baseZ: 999999,
                centerY: centerY,
                css: {
                    top: '20%',
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: '#000',
                    opacity: 0.05,
                    cursor: 'wait'
                }
            });
        } else { // page blocking
            $.blockUI({
                message: html,
                baseZ: 999999,
                css: {
                    border: '0',
                    padding: '0',
                    backgroundColor: 'none'
                },
                overlayCSS: {
                    backgroundColor: '#555',
                    opacity: 0.05,
                    cursor: 'wait'
                }
            });
        }
    };

    $.easyUnblockUI = function (container) {
        if (container == undefined) {
            $.unblockUI();
        } else {
            $(container).unblock({
                onUnblock: function () {
                    $(container).css('position', '');
                    $(container).css('zoom', '');
                }
            });
        }
    };

    $.showToastr = function (toastrMessage, toastrType, options) {

        var defaults = {
            "closeButton": false,
            "debug": false,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "1000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        var opt = defaults;

        if (typeof options == "object") {
            opt = $.extend(defaults, options);
        }

        toastr.options = opt;

        toastrType = typeof toastrType !== 'undefined' ? toastrType : 'success';

        toastr[toastrType](toastrMessage);
    };

    $.viewModal = function (selector, onLoad) {

        var modalContent = $(selector + ' .modal-content').html();

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal');

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function () {
            $(this).find('.modal-content').html(modalContent);
            $(this).data('bs.modal', null);

        });
    };

    $.ajaxModal = function (selector, url, onLoad) {

        var modalContent = $(selector + ' .modal-content').html();

        $(selector).removeData('bs.modal').modal({
            show: true
        });
        $(selector + ' .modal-content').removeData('bs.modal').load(url);

        // Trigger to do stuff with form loaded in modal
        $(document).trigger("ajaxPageLoad");

        // Call onload method if it was passed in function call
        if (typeof onLoad != "undefined") {
            onLoad();
        }

        // Reset modal when it hides
        $(selector).on('hidden.bs.modal', function () {
            $(this).find('.modal-content').html(modalContent);
            $(this).data('bs.modal', null);
        });
    };

    $.showErrors = function (object) {
        var keys = Object.keys(object);

        $(".has-error").find(".help-block").remove();
        $(".has-error").removeClass("has-error");

        for (var i = 0; i < keys.length; i++) {
            var ele = $("[name='" + keys[i] + "']");
            if (ele.length == 0) {
                ele = $("#" + keys[i]);
            }
            var grp = ele.closest(".form-group");
            $(grp).find(".help-block").remove();
            var helpBlockContainer = $(grp).find("div:first");

            if (helpBlockContainer.length == 0) {
                helpBlockContainer = $(grp);
            }

            helpBlockContainer.append('<div class="help-block">' + object[keys[i]] + ' </div>');
            $(grp).addClass("has-error");
        }
    };
    $.dataTable = function (options,el="#myTable", url) {
        let defaults = {
            dom: 'Blfrtip', //// dom:"<'row'<'col-5'l>i<'col'p>>",,
            responsive: true,
            
            processing: true,
            serverSide: true,
            order: [[0, 'desc']],
            language: languageOptions(),
            buttons: [
                {
                    "extend": 'excel',
                    "text": '<i class="fa fa-file-excel" ></i> Excel',
                    "titleAttr": 'Excel',
                    "action": $.dataTableExport,
                    exportOptions: {
                       columns: function (idx, data, node) {
                                if (node.innerHTML == "Action" || !$(node).is(':visible') || node.innerHTML == "" || node.classList.contains('noexport'))
                                    return false;
                                return true;
                            }
                    }
                },
                {
                    "extend": 'csv',
                    "text": '<i class="fa fa-file-text" ></i> CSV',
                    "titleAttr": 'CSV',
                    "class":'hide',
                    "action": $.dataTableExport,
                    exportOptions: {
                       columns: function (idx, data, node) {
                                if (node.innerHTML == "Action" || !$(node).is(':visible') || node.innerHTML == "" || node.classList.contains('noexport'))
                                    return false;
                                return true;
                            }
                    }
                },
                {
                    "extend": 'pdf',
                    "text": '<i class="fa fa-file-pdf" ></i> PDF',
                    "titleAttr": 'PDF',
                    "action": $.dataTableExport,
                    exportOptions: {
                       columns: function (idx, data, node) {
                                if (node.innerHTML == "Action" || !$(node).is(':visible') || node.innerHTML == "" || node.classList.contains('noexport'))
                                    return false;
                                return true;
                            }
                    },
                    customize: function ( doc ) {
                        var now = new Date();
                var formattedDate = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();

                    
                   doc.content.splice(0, 0, {
                    columns: [
                        {
                           image: 'data:image/png;base64,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'
                        },
                        {
                            // Title in the center
                            text: doc.content[0].text,  // Copy the default title
                            fontSize: 16,
                            bold: true,
                            alignment: 'center',
                            margin: [0, 0, 0, 0]
                        },
                        {
                            // Date on the right
                            text: "Exported On: "+formattedDate,
                            fontSize: 10,
                            alignment: 'right',
                            margin: [0, 0, 0, 0]
                        }
                    ],
                    margin: [0, 0, 0, 12]
                });

                                doc.content.splice(1, 1);
                                doc.content.splice(1, 0, {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 575, // Page width
                            y2: 0,
                            lineWidth: 1
                        }
                    ],
                    margin: [0, 0, 0, 12]
                });
                
                
                doc.content[doc.content.length - 1].table.widths = '*'.repeat(doc.content[doc.content.length - 1].table.body[0].length).split('').map(() => '*');

               /* doc['footer'] = (currentPage, pageCount) => {
                    return {
                        columns: [
                            {
                                text: `Page ${currentPage} of ${pageCount}`,
                                alignment: 'center',
                                margin: [0, 10]
                            }
                        ],
                        margin: [10, 0]
                    };
                };*/
                 doc.pageMargins = [10, 50, 10, 10];
               
                }
                },
                /*{
                    "extend": 'print',
                    "text": '<i class="fa fa-print" ></i> Web',
                    "titleAttr": 'Open in new tab to print result',
                    "action": $.dataTableExport,
                    exportOptions: {
                       columns: function (idx, data, node) {
                                if (node.innerHTML == "Action" || !$(node).is(':visible') || node.innerHTML == "" || node.classList.contains('noexport'))
                                    return false;
                                return true;
                            }
                    }
                },*/
                {
                    "extend": 'colvis',
                    "text": '<i class="fa fa-columns" ></i> Cols',
                    "titleAttr": 'colvis'
                }
            ],
            fnDrawCallback: function (oSettings) {
                $("body").removeClass("control-sidebar-slide-open");
                $("body").tooltip({
                    selector: '[data-toggle="tooltip"]'
                });
            }
        };

        let opt = defaults;

        // Extend user-set options over defaults
        if (options) {
            opt = $.extend(defaults, options);
        }

        // check ajax (Data) function
        if (typeof opt.ajax !== "function" && url) {
            opt.ajax = {'url': url};
        }

        let table = $(el).DataTable(opt);
        
        //new $.fn.dataTable.FixedHeader(table);
        
        return table;
    };
    $.dataTableExport = function (e, dt, button, config) {
        var self = this;
        var oldStart = dt.settings()[0]._iDisplayStart;
        dt.one('preXhr', function (e, s, data) {
            // Just this once, load all data from the server...
            data.start = 0;
            data.length = 2147483647;
            dt.one('preDraw', function (e, settings) {
                // Call the original action function
                if (button[0].className.indexOf('buttons-copy') >= 0) {
                    $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                    $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                    $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                    $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                            $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
                } else if (button[0].className.indexOf('buttons-print') >= 0) {
                    $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
                }
                dt.one('preXhr', function (e, s, data) {
                    // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                    // Set the property to what it was before exporting.
                    settings._iDisplayStart = oldStart;
                    data.start = oldStart;
                });
                // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
                setTimeout(dt.ajax.reload, 0);
                // Prevent rendering of the full data to the DOM
                return false;
            });
        });
        // Requery the server with the new one-time export settings
        dt.ajax.reload();
    };
    $.select2Link = function (parent, child, url, select2Options) {
        var afterActions = [];
        var options = select2Options || {};

        this.then = function(callback) {
            afterActions.push(callback);
            return this;
        };

        parent.select2(select2Options).on("change", function (e) {
            child = $(this).closest('tr').find('select.items').first();
            
            child.prop("disabled", true);

            var _this = $(this);
            $.getJSON(url.replace(':parentId:', $(this).val()), function(items) {
                var newOptions = '<option value="">-- Select --</option>';
                for(let i=0;i<items.length;i++) {
                    newOptions += '<option value="'+ items[i].id +'">'+ items[i].name +'</option>';
                }

                child.select2('destroy').html(newOptions).prop("disabled", false)
                    .select2(options);
                
                afterActions.forEach(function (callback) {
                    callback(_this, child, items);
                });
            });
        });
    }
    $.scroll = function (to){
        $('html, body').animate({
                scrollTop: to
            }, 800, function(){});
    }

})(jQuery);

// Prevent submit of ajax form
$(document).on("ready", function () {
    $(".ajax-form").on("submit", function (e) {
        e.preventDefault();
    });
});
$(document).on("ajaxPageLoad", function () {
    $(".ajax-form").on("submit", function (e) {
        e.preventDefault();
    })
});

/**
 * bootbox.js v4.4.0
 *
 * http://bootboxjs.com/license.txt
 */
!function (a, b) {
    "use strict";
    "function" == typeof define && define.amd ? define(["jquery"], b) : "object" == typeof exports ? module.exports = b(require("jquery")) : a.bootbox = b(a.jQuery)
}(this, function a(b, c) {
    "use strict";
    function d(a) {
        var b = q[o.locale];
        return b ? b[a] : q.en[a]
    }
    function e(a, c, d) {
        a.stopPropagation(), a.preventDefault();
        var e = b.isFunction(d) && d.call(c, a) === !1;
        e || c.modal("hide")
    }
    function f(a) {
        var b, c = 0;
        for (b in a)
            c++;
        return c
    }
    function g(a, c) {
        var d = 0;
        b.each(a, function (a, b) {
            c(a, b, d++)
        })
    }
    function h(a) {
        var c, d;
        if ("object" != typeof a)
            throw new Error("Please supply an object of options");
        if (!a.message)
            throw new Error("Please specify a message");
        return a = b.extend({}, o, a), a.buttons || (a.buttons = {}), c = a.buttons, d = f(c), g(c, function (a, e, f) {
            if (b.isFunction(e) && (e = c[a] = {callback: e}), "object" !== b.type(e))
                throw new Error("button with key " + a + " must be an object");
            e.label || (e.label = a), e.className || (e.className = 2 >= d && f === d - 1 ? "btn-primary" : "btn-default")
        }), a
    }
    function i(a, b) {
        var c = a.length, d = {};
        if (1 > c || c > 2)
            throw new Error("Invalid argument length");
        return 2 === c || "string" == typeof a[0] ? (d[b[0]] = a[0], d[b[1]] = a[1]) : d = a[0], d
    }
    function j(a, c, d) {
        return b.extend(!0, {}, a, i(c, d))
    }
    function k(a, b, c, d) {
        var e = {className: "bootbox-" + a, buttons: l.apply(null, b)};
        return m(j(e, d, c), b)
    }
    function l() {
        for (var a = {}, b = 0, c = arguments.length; c > b; b++) {
            var e = arguments[b], f = e.toLowerCase(), g = e.toUpperCase();
            a[f] = {label: d(g)}
        }
        return a
    }
    function m(a, b) {
        var d = {};
        return g(b, function (a, b) {
            d[b] = !0
        }), g(a.buttons, function (a) {
            if (d[a] === c)
                throw new Error("button key " + a + " is not allowed (options are " + b.join("\n") + ")")
        }), a
    }
    var n = {dialog: "<div class='bootbox modal' tabindex='-1' role='dialog'><div class='modal-dialog'><div class='modal-content'><div class='modal-body'><div class='bootbox-body'></div></div></div></div></div>", header: "<div class='modal-header'><h4 class='modal-title'></h4></div>", footer: "<div class='modal-footer'></div>", closeButton: "<button type='button' class='bootbox-close-button close' data-dismiss='modal' aria-hidden='true'>&times;</button>", form: "<form class='bootbox-form'></form>", inputs: {text: "<input class='bootbox-input bootbox-input-text form-control' autocomplete=off type=text />", textarea: "<textarea class='bootbox-input bootbox-input-textarea form-control'></textarea>", email: "<input class='bootbox-input bootbox-input-email form-control' autocomplete='off' type='email' />", select: "<select class='bootbox-input bootbox-input-select form-control'></select>", checkbox: "<div class='checkbox'><label><input class='bootbox-input bootbox-input-checkbox' type='checkbox' /></label></div>", date: "<input class='bootbox-input bootbox-input-date form-control' autocomplete=off type='date' />", time: "<input class='bootbox-input bootbox-input-time form-control' autocomplete=off type='time' />", number: "<input class='bootbox-input bootbox-input-number form-control' autocomplete=off type='number' />", password: "<input class='bootbox-input bootbox-input-password form-control' autocomplete='off' type='password' />"}}, o = {locale: "en", backdrop: "static", animate: !0, className: null, closeButton: !0, show: !0, container: "body"}, p = {};
    p.alert = function () {
        var a;
        if (a = k("alert", ["ok"], ["message", "callback"], arguments), a.callback && !b.isFunction(a.callback))
            throw new Error("alert requires callback property to be a function when provided");
        return a.buttons.ok.callback = a.onEscape = function () {
            return b.isFunction(a.callback) ? a.callback.call(this) : !0
        }, p.dialog(a)
    }, p.confirm = function () {
        var a;
        if (a = k("confirm", ["cancel", "confirm"], ["message", "callback"], arguments), a.buttons.cancel.callback = a.onEscape = function () {
            return a.callback.call(this, !1)
        }, a.buttons.confirm.callback = function () {
            return a.callback.call(this, !0)
        }, !b.isFunction(a.callback))
            throw new Error("confirm requires a callback");
        return p.dialog(a)
    }, p.prompt = function () {
        var a, d, e, f, h, i, k;
        if (f = b(n.form), d = {className: "bootbox-prompt", buttons: l("cancel", "confirm"), value: "", inputType: "text"}, a = m(j(d, arguments, ["title", "callback"]), ["cancel", "confirm"]), i = a.show === c ? !0 : a.show, a.message = f, a.buttons.cancel.callback = a.onEscape = function () {
            return a.callback.call(this, null)
        }, a.buttons.confirm.callback = function () {
            var c;
            switch (a.inputType) {
                case"text":
                case"textarea":
                case"email":
                case"select":
                case"date":
                case"time":
                case"number":
                case"password":
                    c = h.val();
                    break;
                case"checkbox":
                    var d = h.find("input:checked");
                    c = [], g(d, function (a, d) {
                        c.push(b(d).val())
                    })
            }
            return a.callback.call(this, c)
        }, a.show = !1, !a.title)
            throw new Error("prompt requires a title");
        if (!b.isFunction(a.callback))
            throw new Error("prompt requires a callback");
        if (!n.inputs[a.inputType])
            throw new Error("invalid prompt type");
        switch (h = b(n.inputs[a.inputType]), a.inputType) {
            case"text":
            case"textarea":
            case"email":
            case"date":
            case"time":
            case"number":
            case"password":
                h.val(a.value);
                break;
            case"select":
                var o = {};
                if (k = a.inputOptions || [], !b.isArray(k))
                    throw new Error("Please pass an array of input options");
                if (!k.length)
                    throw new Error("prompt with select requires options");
                g(k, function (a, d) {
                    var e = h;
                    if (d.value === c || d.text === c)
                        throw new Error("given options in wrong format");
                    d.group && (o[d.group] || (o[d.group] = b("<optgroup/>").attr("label", d.group)), e = o[d.group]), e.append("<option value='" + d.value + "'>" + d.text + "</option>")
                }), g(o, function (a, b) {
                    h.append(b)
                }), h.val(a.value);
                break;
            case"checkbox":
                var q = b.isArray(a.value) ? a.value : [a.value];
                if (k = a.inputOptions || [], !k.length)
                    throw new Error("prompt with checkbox requires options");
                if (!k[0].value || !k[0].text)
                    throw new Error("given options in wrong format");
                h = b("<div/>"), g(k, function (c, d) {
                    var e = b(n.inputs[a.inputType]);
                    e.find("input").attr("value", d.value), e.find("label").append(d.text), g(q, function (a, b) {
                        b === d.value && e.find("input").prop("checked", !0)
                    }), h.append(e)
                })
        }
        return a.placeholder && h.attr("placeholder", a.placeholder), a.pattern && h.attr("pattern", a.pattern), a.maxlength && h.attr("maxlength", a.maxlength), f.append(h), f.on("submit", function (a) {
            a.preventDefault(), a.stopPropagation(), e.find(".btn-primary").click()
        }), e = p.dialog(a), e.off("shown.bs.modal"), e.on("shown.bs.modal", function () {
            h.focus()
        }), i === !0 && e.modal("show"), e
    }, p.dialog = function (a) {
        a = h(a);
        var d = b(n.dialog), f = d.find(".modal-dialog"), i = d.find(".modal-body"), j = a.buttons, k = "", l = {onEscape: a.onEscape};
        if (b.fn.modal === c)
            throw new Error("$.fn.modal is not defined; please double check you have included the Bootstrap JavaScript library. See http://getbootstrap.com/javascript/ for more details.");
        if (g(j, function (a, b) {
            k += "<button data-bb-handler='" + a + "' type='button' class='btn " + b.className + "'>" + b.label + "</button>", l[a] = b.callback
        }), i.find(".bootbox-body").html(a.message), a.animate === !0 && d.addClass("fade"), a.className && d.addClass(a.className), "large" === a.size ? f.addClass("modal-lg") : "small" === a.size && f.addClass("modal-sm"), a.title && i.before(n.header), a.closeButton) {
            var m = b(n.closeButton);
            a.title ? d.find(".modal-header").prepend(m) : m.css("margin-top", "-10px").prependTo(i)
        }
        return a.title && d.find(".modal-title").html(a.title), k.length && (i.after(n.footer), d.find(".modal-footer").html(k)), d.on("hidden.bs.modal", function (a) {
            a.target === this && d.remove()
        }), d.on("shown.bs.modal", function () {
            d.find(".btn-primary:first").focus()
        }), "static" !== a.backdrop && d.on("click.dismiss.bs.modal", function (a) {
            d.children(".modal-backdrop").length && (a.currentTarget = d.children(".modal-backdrop").get(0)), a.target === a.currentTarget && d.trigger("escape.close.bb")
        }), d.on("escape.close.bb", function (a) {
            l.onEscape && e(a, d, l.onEscape)
        }), d.on("click", ".modal-footer button", function (a) {
            var c = b(this).data("bb-handler");
            e(a, d, l[c])
        }), d.on("click", ".bootbox-close-button", function (a) {
            e(a, d, l.onEscape)
        }), d.on("keyup", function (a) {
            27 === a.which && d.trigger("escape.close.bb")
        }), b(a.container).append(d), d.modal({backdrop: a.backdrop ? "static" : !1, keyboard: !1, show: !1}), a.show && d.modal("show"), d
    }, p.setDefaults = function () {
        var a = {};
        2 === arguments.length ? a[arguments[0]] = arguments[1] : a = arguments[0], b.extend(o, a)
    }, p.hideAll = function () {
        return b(".bootbox").modal("hide"), p
    };
    var q = {bg_BG: {OK: "ÐžÐº", CANCEL: "ÐžÑ‚ÐºÐ°Ð·", CONFIRM: "ÐŸÐ¾Ñ‚Ð²ÑŠÑ€Ð¶Ð´Ð°Ð²Ð°Ð¼"}, br: {OK: "OK", CANCEL: "Cancelar", CONFIRM: "Sim"}, cs: {OK: "OK", CANCEL: "ZruÅ¡it", CONFIRM: "Potvrdit"}, da: {OK: "OK", CANCEL: "Annuller", CONFIRM: "Accepter"}, de: {OK: "OK", CANCEL: "Abbrechen", CONFIRM: "Akzeptieren"}, el: {OK: "Î•Î½Ï„Î¬Î¾ÎµÎ¹", CANCEL: "Î‘ÎºÏÏÏ‰ÏƒÎ·", CONFIRM: "Î•Ï€Î¹Î²ÎµÎ²Î±Î¯Ï‰ÏƒÎ·"}, en: {OK: "OK", CANCEL: "Cancel", CONFIRM: "OK"}, es: {OK: "OK", CANCEL: "Cancelar", CONFIRM: "Aceptar"}, et: {OK: "OK", CANCEL: "Katkesta", CONFIRM: "OK"}, fa: {OK: "Ù‚Ø¨ÙˆÙ„", CANCEL: "Ù„ØºÙˆ", CONFIRM: "ØªØ§ÛŒÛŒØ¯"}, fi: {OK: "OK", CANCEL: "Peruuta", CONFIRM: "OK"}, fr: {OK: "OK", CANCEL: "Annuler", CONFIRM: "D'accord"}, he: {OK: "××™×©×•×¨", CANCEL: "×‘×™×˜×•×œ", CONFIRM: "××™×©×•×¨"}, hu: {OK: "OK", CANCEL: "MÃ©gsem", CONFIRM: "MegerÅ‘sÃ­t"}, hr: {OK: "OK", CANCEL: "Odustani", CONFIRM: "Potvrdi"}, id: {OK: "OK", CANCEL: "Batal", CONFIRM: "OK"}, it: {OK: "OK", CANCEL: "Annulla", CONFIRM: "Conferma"}, ja: {OK: "OK", CANCEL: "ã‚­ãƒ£ãƒ³ã‚»ãƒ«", CONFIRM: "ç¢ºèª"}, lt: {OK: "Gerai", CANCEL: "AtÅ¡aukti", CONFIRM: "Patvirtinti"}, lv: {OK: "Labi", CANCEL: "Atcelt", CONFIRM: "ApstiprinÄt"}, nl: {OK: "OK", CANCEL: "Annuleren", CONFIRM: "Accepteren"}, no: {OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK"}, pl: {OK: "OK", CANCEL: "Anuluj", CONFIRM: "PotwierdÅº"}, pt: {OK: "OK", CANCEL: "Cancelar", CONFIRM: "Confirmar"}, ru: {OK: "OK", CANCEL: "ÐžÑ‚Ð¼ÐµÐ½Ð°", CONFIRM: "ÐŸÑ€Ð¸Ð¼ÐµÐ½Ð¸Ñ‚ÑŒ"}, sq: {OK: "OK", CANCEL: "Anulo", CONFIRM: "Prano"}, sv: {OK: "OK", CANCEL: "Avbryt", CONFIRM: "OK"}, th: {OK: "à¸•à¸à¸¥à¸‡", CANCEL: "à¸¢à¸à¹€à¸¥à¸´à¸", CONFIRM: "à¸¢à¸·à¸™à¸¢à¸±à¸™"}, tr: {OK: "Tamam", CANCEL: "Ä°ptal", CONFIRM: "Onayla"}, zh_CN: {OK: "OK", CANCEL: "å–æ¶ˆ", CONFIRM: "ç¡®è®¤"}, zh_TW: {OK: "OK", CANCEL: "å–æ¶ˆ", CONFIRM: "ç¢ºèª"}};
    return p.addLocale = function (a, c) {
        return b.each(["OK", "CANCEL", "CONFIRM"], function (a, b) {
            if (!c[b])
                throw new Error("Please supply a translation for '" + b + "'")
        }), q[a] = {OK: c.OK, CANCEL: c.CANCEL, CONFIRM: c.CONFIRM}, p
    }, p.removeLocale = function (a) {
        return delete q[a], p
    }, p.setLocale = function (a) {
        return p.setDefaults("locale", a)
    }, p.init = function (c) {
        return a(c || b)
    }, p
});

// Toastr
!function (a) {
    a(["jquery"], function (a) {
        return function () {
            function b(a, b, c) {
                return o({type: u.error, iconClass: p().iconClasses.error, message: a, optionsOverride: c, title: b})
            }
            function c(b, c) {
                return b || (b = p()), r = a("#" + b.containerId), r.length ? r : (c && (r = l(b)), r)
            }
            function d(a, b, c) {
                return o({type: u.info, iconClass: p().iconClasses.info, message: a, optionsOverride: c, title: b})
            }
            function e(a) {
                s = a
            }
            function f(a, b, c) {
                return o({type: u.success, iconClass: p().iconClasses.success, message: a, optionsOverride: c, title: b})
            }
            function g(a, b, c) {
                return o({type: u.warning, iconClass: p().iconClasses.warning, message: a, optionsOverride: c, title: b})
            }
            function h(a) {
                var b = p();
                r || c(b), k(a, b) || j(b)
            }
            function i(b) {
                var d = p();
                return r || c(d), b && 0 === a(":focus", b).length ? void q(b) : void(r.children().length && r.remove())
            }
            function j(b) {
                for (var c = r.children(), d = c.length - 1; d >= 0; d--)
                    k(a(c[d]), b)
            }
            function k(b, c) {
                return b && 0 === a(":focus", b).length ? (b[c.hideMethod]({duration: c.hideDuration, easing: c.hideEasing, complete: function () {
                        q(b)
                    }}), !0) : !1
            }
            function l(b) {
                return r = a("<div/>").attr("id", b.containerId).addClass(b.positionClass).attr("aria-live", "polite").attr("role", "alert"), r.appendTo(a(b.target)), r
            }
            function m() {
                return{tapToDismiss: !0, toastClass: "toast", containerId: "toast-container", debug: !1, showMethod: "fadeIn", showDuration: 300, showEasing: "swing", onShown: void 0, hideMethod: "fadeOut", hideDuration: 1e3, hideEasing: "swing", onHidden: void 0, extendedTimeOut: 1e3, iconClasses: {error: "toast-error", info: "toast-info", success: "toast-success", warning: "toast-warning"}, iconClass: "toast-info", positionClass: "toast-top-right", timeOut: 5e3, titleClass: "toast-title", messageClass: "toast-message", target: "body", closeHtml: "<button>&times;</button>", newestOnTop: !0}
            }
            function n(a) {
                s && s(a)
            }
            function o(b) {
                function d(b) {
                    return!a(":focus", j).length || b ? j[g.hideMethod]({duration: g.hideDuration, easing: g.hideEasing, complete: function () {
                            q(j), g.onHidden && "hidden" !== o.state && g.onHidden(), o.state = "hidden", o.endTime = new Date, n(o)
                        }}) : void 0
                }
                function e() {
                    (g.timeOut > 0 || g.extendedTimeOut > 0) && (i = setTimeout(d, g.extendedTimeOut))
                }
                function f() {
                    clearTimeout(i), j.stop(!0, !0)[g.showMethod]({duration: g.showDuration, easing: g.showEasing})
                }
                var g = p(), h = b.iconClass || g.iconClass;
                "undefined" != typeof b.optionsOverride && (g = a.extend(g, b.optionsOverride), h = b.optionsOverride.iconClass || h), t++, r = c(g, !0);
                var i = null, j = a("<div/>"), k = a("<div/>"), l = a("<div/>"), m = a(g.closeHtml), o = {toastId: t, state: "visible", startTime: new Date, options: g, map: b};
                return b.iconClass && j.addClass(g.toastClass).addClass(h), b.title && (k.append(b.title).addClass(g.titleClass), j.append(k)), b.message && (l.append(b.message).addClass(g.messageClass), j.append(l)), g.closeButton && (m.addClass("toast-close-button").attr("role", "button"), j.prepend(m)), j.hide(), g.newestOnTop ? r.prepend(j) : r.append(j), j[g.showMethod]({duration: g.showDuration, easing: g.showEasing, complete: g.onShown}), g.timeOut > 0 && (i = setTimeout(d, g.timeOut)), j.hover(f, e), !g.onclick && g.tapToDismiss && j.click(d), g.closeButton && m && m.click(function (a) {
                    a.stopPropagation ? a.stopPropagation() : void 0 !== a.cancelBubble && a.cancelBubble !== !0 && (a.cancelBubble = !0), d(!0)
                }), g.onclick && j.click(function () {
                    g.onclick(), d()
                }), n(o), g.debug && console && console.log(o), j
            }
            function p() {
                return a.extend({}, m(), v.options)
            }
            function q(a) {
                r || (r = c()), a.is(":visible") || (a.remove(), a = null, 0 === r.children().length && r.remove())
            }
            var r, s, t = 0, u = {error: "error", info: "info", success: "success", warning: "warning"}, v = {clear: h, remove: i, error: b, getContainer: c, info: d, options: {}, subscribe: e, success: f, version: "2.0.3", warning: g};
            return v
        }()
    })
}("function" == typeof define && define.amd ? define : function (a, b) {
    "undefined" != typeof module && module.exports ? module.exports = b(require("jquery")) : window.toastr = b(window.jQuery)
});

/*!
 * jQuery blockUI plugin
 * Version 2.70.0-2014.11.23
 * Requires jQuery v1.7 or later
 *
 * Examples at: http://malsup.com/jquery/block/
 * Copyright (c) 2007-2013 M. Alsup
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Thanks to Amir-Hossein Sobhi for some excellent contributions!
 */
!function () {
    "use strict";
    function e(e) {
        function t(t, n) {
            var s, h, k = t == window, y = n && void 0 !== n.message ? n.message : void 0;
            if (n = e.extend({}, e.blockUI.defaults, n || {}), !n.ignoreIfBlocked || !e(t).data("blockUI.isBlocked")) {
                if (n.overlayCSS = e.extend({}, e.blockUI.defaults.overlayCSS, n.overlayCSS || {}), s = e.extend({}, e.blockUI.defaults.css, n.css || {}), n.onOverlayClick && (n.overlayCSS.cursor = "pointer"), h = e.extend({}, e.blockUI.defaults.themedCSS, n.themedCSS || {}), y = void 0 === y ? n.message : y, k && p && o(window, {fadeOut: 0}), y && "string" != typeof y && (y.parentNode || y.jquery)) {
                    var m = y.jquery ? y[0] : y, v = {};
                    e(t).data("blockUI.history", v), v.el = m, v.parent = m.parentNode, v.display = m.style.display, v.position = m.style.position, v.parent && v.parent.removeChild(m)
                }
                e(t).data("blockUI.onUnblock", n.onUnblock);
                var g, I, w, U, x = n.baseZ;
                g = e(r || n.forceIframe ? '<iframe class="blockUI" style="z-index:' + x++ + ';display:none;border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="' + n.iframeSrc + '"></iframe>' : '<div class="blockUI" style="display:none"></div>'), I = e(n.theme ? '<div class="blockUI blockOverlay ui-widget-overlay" style="z-index:' + x++ + ';display:none"></div>' : '<div class="blockUI blockOverlay" style="z-index:' + x++ + ';display:none;border:none;margin:0;padding:0;width:100%;height:100%;top:0;left:0"></div>'), n.theme && k ? (U = '<div class="blockUI ' + n.blockMsgClass + ' blockPage ui-dialog ui-widget ui-corner-all" style="z-index:' + (x + 10) + ';display:none;position:fixed">', n.title && (U += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">' + (n.title || "&nbsp;") + "</div>"), U += '<div class="ui-widget-content ui-dialog-content"></div>', U += "</div>") : n.theme ? (U = '<div class="blockUI ' + n.blockMsgClass + ' blockElement ui-dialog ui-widget ui-corner-all" style="z-index:' + (x + 10) + ';display:none;position:absolute">', n.title && (U += '<div class="ui-widget-header ui-dialog-titlebar ui-corner-all blockTitle">' + (n.title || "&nbsp;") + "</div>"), U += '<div class="ui-widget-content ui-dialog-content"></div>', U += "</div>") : U = k ? '<div class="blockUI ' + n.blockMsgClass + ' blockPage" style="z-index:' + (x + 10) + ';display:none;position:fixed"></div>' : '<div class="blockUI ' + n.blockMsgClass + ' blockElement" style="z-index:' + (x + 10) + ';display:none;position:absolute"></div>', w = e(U), y && (n.theme ? (w.css(h), w.addClass("ui-widget-content")) : w.css(s)), n.theme || I.css(n.overlayCSS), I.css("position", k ? "fixed" : "absolute"), (r || n.forceIframe) && g.css("opacity", 0);
                var C = [g, I, w], S = e(k ? "body" : t);
                e.each(C, function () {
                    this.appendTo(S)
                }), n.theme && n.draggable && e.fn.draggable && w.draggable({handle: ".ui-dialog-titlebar", cancel: "li"});
                var O = f && (!e.support.boxModel || e("object,embed", k ? null : t).length > 0);
                if (u || O) {
                    if (k && n.allowBodyStretch && e.support.boxModel && e("html,body").css("height", "100%"), (u || !e.support.boxModel) && !k)
                        var E = d(t, "borderTopWidth"), T = d(t, "borderLeftWidth"), M = E ? "(0 - " + E + ")" : 0, B = T ? "(0 - " + T + ")" : 0;
                    e.each(C, function (e, t) {
                        var o = t[0].style;
                        if (o.position = "absolute", 2 > e)
                            k ? o.setExpression("height", "Math.max(document.body.scrollHeight, document.body.offsetHeight) - (jQuery.support.boxModel?0:" + n.quirksmodeOffsetHack + ') + "px"') : o.setExpression("height", 'this.parentNode.offsetHeight + "px"'), k ? o.setExpression("width", 'jQuery.support.boxModel && document.documentElement.clientWidth || document.body.clientWidth + "px"') : o.setExpression("width", 'this.parentNode.offsetWidth + "px"'), B && o.setExpression("left", B), M && o.setExpression("top", M);
                        else if (n.centerY)
                            k && o.setExpression("top", '(document.documentElement.clientHeight || document.body.clientHeight) / 2 - (this.offsetHeight / 2) + (blah = document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + "px"'), o.marginTop = 0;
                        else if (!n.centerY && k) {
                            var i = n.css && n.css.top ? parseInt(n.css.top, 10) : 0, s = "((document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop) + " + i + ') + "px"';
                            o.setExpression("top", s)
                        }
                    })
                }
                if (y && (n.theme ? w.find(".ui-widget-content").append(y) : w.append(y), (y.jquery || y.nodeType) && e(y).show()), (r || n.forceIframe) && n.showOverlay && g.show(), n.fadeIn) {
                    var j = n.onBlock ? n.onBlock : c, H = n.showOverlay && !y ? j : c, z = y ? j : c;
                    n.showOverlay && I._fadeIn(n.fadeIn, H), y && w._fadeIn(n.fadeIn, z)
                } else
                    n.showOverlay && I.show(), y && w.show(), n.onBlock && n.onBlock.bind(w)();
                if (i(1, t, n), k ? (p = w[0], b = e(n.focusableElements, p), n.focusInput && setTimeout(l, 20)) : a(w[0], n.centerX, n.centerY), n.timeout) {
                    var W = setTimeout(function () {
                        k ? e.unblockUI(n) : e(t).unblock(n)
                    }, n.timeout);
                    e(t).data("blockUI.timeout", W)
                }
            }
        }
        function o(t, o) {
            var s, l = t == window, a = e(t), d = a.data("blockUI.history"), c = a.data("blockUI.timeout");
            c && (clearTimeout(c), a.removeData("blockUI.timeout")), o = e.extend({}, e.blockUI.defaults, o || {}), i(0, t, o), null === o.onUnblock && (o.onUnblock = a.data("blockUI.onUnblock"), a.removeData("blockUI.onUnblock"));
            var r;
            r = l ? e("body").children().filter(".blockUI").add("body > .blockUI") : a.find(">.blockUI"), o.cursorReset && (r.length > 1 && (r[1].style.cursor = o.cursorReset), r.length > 2 && (r[2].style.cursor = o.cursorReset)), l && (p = b = null), o.fadeOut ? (s = r.length, r.stop().fadeOut(o.fadeOut, function () {
                0 === --s && n(r, d, o, t)
            })) : n(r, d, o, t)
        }
        function n(t, o, n, i) {
            var s = e(i);
            if (!s.data("blockUI.isBlocked")) {
                t.each(function () {
                    this.parentNode && this.parentNode.removeChild(this)
                }), o && o.el && (o.el.style.display = o.display, o.el.style.position = o.position, o.el.style.cursor = "default", o.parent && o.parent.appendChild(o.el), s.removeData("blockUI.history")), s.data("blockUI.static") && s.css("position", "static"), "function" == typeof n.onUnblock && n.onUnblock(i, n);
                var l = e(document.body), a = l.width(), d = l[0].style.width;
                l.width(a - 1).width(a), l[0].style.width = d
            }
        }
        function i(t, o, n) {
            var i = o == window, l = e(o);
            if ((t || (!i || p) && (i || l.data("blockUI.isBlocked"))) && (l.data("blockUI.isBlocked", t), i && n.bindEvents && (!t || n.showOverlay))) {
                var a = "mousedown mouseup keydown keypress keyup touchstart touchend touchmove";
                t ? e(document).bind(a, n, s) : e(document).unbind(a, s)
            }
        }
        function s(t) {
            if ("keydown" === t.type && t.keyCode && 9 == t.keyCode && p && t.data.constrainTabKey) {
                var o = b, n = !t.shiftKey && t.target === o[o.length - 1], i = t.shiftKey && t.target === o[0];
                if (n || i)
                    return setTimeout(function () {
                        l(i)
                    }, 10), !1
            }
            var s = t.data, a = e(t.target);
            return a.hasClass("blockOverlay") && s.onOverlayClick && s.onOverlayClick(t), a.parents("div." + s.blockMsgClass).length > 0 ? !0 : 0 === a.parents().children().filter("div.blockUI").length
        }
        function l(e) {
            if (b) {
                var t = b[e === !0 ? b.length - 1 : 0];
                t && t.focus()
            }
        }
        function a(e, t, o) {
            var n = e.parentNode, i = e.style, s = (n.offsetWidth - e.offsetWidth) / 2 - d(n, "borderLeftWidth"), l = (n.offsetHeight - e.offsetHeight) / 2 - d(n, "borderTopWidth");
            t && (i.left = s > 0 ? s + "px" : "0"), o && (i.top = l > 0 ? l + "px" : "0")
        }
        function d(t, o) {
            return parseInt(e.css(t, o), 10) || 0
        }
        e.fn._fadeIn = e.fn.fadeIn;
        var c = e.noop || function () {}, r = /MSIE/.test(navigator.userAgent), u = /MSIE 6.0/.test(navigator.userAgent) && !/MSIE 8.0/.test(navigator.userAgent), f = (document.documentMode || 0, e.isFunction(document.createElement("div").style.setExpression));
        e.blockUI = function (e) {
            t(window, e)
        }, e.unblockUI = function (e) {
            o(window, e)
        }, e.growlUI = function (t, o, n, i) {
            var s = e('<div class="growlUI"></div>');
            t && s.append("<h1>" + t + "</h1>"), o && s.append("<h2>" + o + "</h2>"), void 0 === n && (n = 3e3);
            var l = function (t) {
                t = t || {}, e.blockUI({message: s, fadeIn: "undefined" != typeof t.fadeIn ? t.fadeIn : 700, fadeOut: "undefined" != typeof t.fadeOut ? t.fadeOut : 1e3, timeout: "undefined" != typeof t.timeout ? t.timeout : n, centerY: !1, showOverlay: !1, onUnblock: i, css: e.blockUI.defaults.growlCSS})
            };
            l();
            s.css("opacity");
            s.mouseover(function () {
                l({fadeIn: 0, timeout: 3e4});
                var t = e(".blockMsg");
                t.stop(), t.fadeTo(300, 1)
            }).mouseout(function () {
                e(".blockMsg").fadeOut(1e3)
            })
        }, e.fn.block = function (o) {
            if (this[0] === window)
                return e.blockUI(o), this;
            var n = e.extend({}, e.blockUI.defaults, o || {});
            return this.each(function () {
                var t = e(this);
                n.ignoreIfBlocked && t.data("blockUI.isBlocked") || t.unblock({fadeOut: 0})
            }), this.each(function () {
                "static" == e.css(this, "position") && (this.style.position = "relative", e(this).data("blockUI.static", !0)), this.style.zoom = 1, t(this, o)
            })
        }, e.fn.unblock = function (t) {
            return this[0] === window ? (e.unblockUI(t), this) : this.each(function () {
                o(this, t)
            })
        }, e.blockUI.version = 2.7, e.blockUI.defaults = {message: "<h1>Please wait...</h1>", title: null, draggable: !0, theme: !1, css: {padding: 0, margin: 0, width: "30%", top: "40%", left: "35%", textAlign: "center", color: "#000", border: "3px solid #aaa", backgroundColor: "#fff", cursor: "wait"}, themedCSS: {width: "30%", top: "40%", left: "35%"}, overlayCSS: {backgroundColor: "#000", opacity: .6, cursor: "wait"}, cursorReset: "default", growlCSS: {width: "350px", top: "10px", left: "", right: "10px", border: "none", padding: "5px", opacity: .6, cursor: "default", color: "#fff", backgroundColor: "#000", "-webkit-border-radius": "10px", "-moz-border-radius": "10px", "border-radius": "10px"}, iframeSrc: /^https/i.test(window.location.href || "") ? "javascript:false" : "about:blank", forceIframe: !1, baseZ: 1e3, centerX: !0, centerY: !0, allowBodyStretch: !0, bindEvents: !0, constrainTabKey: !0, fadeIn: 200, fadeOut: 400, timeout: 0, showOverlay: !0, focusInput: !0, focusableElements: ":input:enabled:visible", onBlock: null, onUnblock: null, onOverlayClick: null, quirksmodeOffsetHack: 4, blockMsgClass: "blockMsg", ignoreIfBlocked: !1};
        var p = null, b = []
    }
    "function" == typeof define && define.amd && define.amd.jQuery ? define(["jquery"], e) : e(jQuery)
}();