(function (win, $) {

    'use strict';

    /**
     * init()
     */
    var init = function () {
        $('.js-btn-action, .js-btn-remove').each(function () {
            var $this = $(this),
                url = $this.data('url'),
                refresh = $this.data('refresh'),
                question = $this.data('question'),
                errorText = $this.data('error-text');

            $this.click(function (ev) {
                ev.preventDefault();
                if (win.confirm(question)) {
                    $.post(url)
                        .done(function () {
                            if (typeof refresh === 'undefined') {
                                win.location.reload(true);
                            } else {
                                win.location.assign(refresh);
                            }
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            var errorString = jqXHR.status + ' - ' + errorThrown;
                            if (typeof errorText !== 'undefined') {
                                errorString = errorText + ' - ' + errorString;
                            }
                            showAlert(errorString);
                        });
                }
            });
        });

        $('.js-nav-tab-default').each(function () {
            var $this = $(this);
            $this.click(function (ev) {
                ev.preventDefault();
                $(this).tab('show');
            });
        });
    };

    var showAlert = function (text) {
        var dialogClass = 'js-modal-alert-dialog',
            textClass = 'js-modal-alert-text',
            dialogSelector = '.' + dialogClass,
            textSelector = '.' + textClass,
            $alert = $(dialogSelector);

        if ($alert.length === 0) {
            $('body').append(
                '<div class="modal fade ' + dialogClass + '" tabindex="-1" role="dialog">' +
                '<div class="modal-dialog" role="document">' +
                '<div class="modal-content">' +
                '<div class="modal-body">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '<span class="' + textClass + '">' +
                text +
                '</span>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>'
            );
            $alert = $(dialogSelector);
        } else {
            $(textSelector).html(text);
        }

        $alert.modal('show');
    };

    /**
     * Main process
     */
    init();

    return {
        init: init
    };

}(window, jQuery));
