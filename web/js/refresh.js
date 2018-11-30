(function (win, $) {

    'use strict';

    var $game = $('.js-game'),
        $maze = $('.js-maze'),
        $panels = $('.js-panels'),
        $btnStart = $('.js-btn-start'),
        $btnStop = $('.js-btn-stop'),
        $btnReset = $('.js-btn-reset'),
        timeout = $game.data('timeout'),
        refreshUrl = $game.data('url'),
        startUrl = $btnStart.data('url'),
        stopUrl = $btnStop.data('url'),
        resetUrl = $btnReset.data('url'),
        refreshTimer = null;

    /**
     * configureButtons()
     */
    var configureButtons = function() {
        $btnStart.click(function(ev) {
            ev.preventDefault();
            startPlaying();
        });

        $btnStop.click(function(ev) {
            ev.preventDefault();
            stopPlaying();
        });

        $btnReset.click(function(ev) {
            ev.preventDefault();
            resetPlaying();
        });
    };

    /**
     * startPlaying()
     */
    var startPlaying = function () {
        $.get(startUrl)
            .done(function() {
                $btnStart.attr('disabled', 'disabled');
                $btnStop.attr('disabled', null);
                refreshMaze();
            })
            .fail(function(jqXHR, textStatus, errorMessage) {
                alert('An error occurred on the server. ' + errorMessage);
            });
    };

    /**
     * stopPlaying()
     */
    var stopPlaying = function () {
        $.get(stopUrl)
            .done(function() {
                $btnStart.attr('disabled', null);
                $btnStop.attr('disabled', 'disabled');
                refreshMaze();
            })
            .fail(function(jqXHR, textStatus, errorMessage) {
                alert('An error occurred on the server. ' + errorMessage);
            });
    };

    /**
     * resetPlaying()
     */
    var resetPlaying = function () {
        $.get(resetUrl)
            .done(function() {
                $btnStart.attr('disabled', null);
                $btnStop.attr('disabled', 'disabled');
                refreshMaze();
            })
            .fail(function(jqXHR, textStatus, errorMessage) {
                alert('An error occurred on the server. ' + errorMessage);
            });
    };

    /**
     * startTimer()
     */
    var startTimer = function () {
        if ($btnStop.attr('disabled') != 'disabled') {
            if (refreshTimer !== null) {
                win.clearTimeout(refreshTimer);
                refreshTimer = null;
            }
            win.location.hash = '';
            win.setTimeout(refreshMaze, 500);
        } else {
            var num = 0;
            var hash = win.location.hash;
            if (hash[0] === "#") {
                num = parseInt(hash.substr(1));
                if (isNaN(num)) {
                    num = 0;
                }
            }
            num++;
            win.location.hash = num;
            if (num < 120) {
                refreshTimer = win.setTimeout(function () {
                    location.reload();
                }, 30000);
            }
        }

    };

    /**
     * refreshMaze()
     */
    var refreshMaze = function () {
        $.ajax({
            'type': 'GET',
            'url': refreshUrl,
            'timeout': timeout
        })
        .done(function(data) {
            $maze.html(data.mazeHtml);
            $panels.html(data.panelsHtml);
            if (data.playing) {
                $btnStop.attr('disabled', null);
            } else {
                $btnStop.attr('disabled', 'disabled');
                if (data.finished) {
                    $btnStart.attr('disabled', 'disabled');
                }
            }
            startTimer();
        })
        .fail(function(jqXHR, textStatus, errorMessage) {
            location.reload();
        });
    };

    /**
     * init()
     */
    var init = function() {
        configureButtons();
        startTimer();
    };

    /**
     * Main process
     */
    init();

    return {
        init: init
    };

}(window, jQuery));
