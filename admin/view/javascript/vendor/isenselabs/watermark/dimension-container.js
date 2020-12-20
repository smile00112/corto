(function($) {
    var 
        selector = {
            container: '[role="dimension-container"]',
            select: '[role="dimension-select"]',
            value: '[role="dimension-value"]'
        },
        attribute = {
            col: 'data-dimension-col',
            value: 'data-dimension-value'
        },
        delimiter = ',',
        fadeOutDuration = 150,
        fadeInDuration = 300;

    $.fn.dimensionContainer = function(c) {
        return this.each(function(index, container) {
            var getValueContainer = function(selectedValue, callbackFound, callbackNotFound) {
                var found = false;

                $(container).find(selector.value).each(function(index, valueContainer) {
                    if ($(valueContainer).attr(attribute.value).split(delimiter).indexOf(selectedValue) >= 0) {
                        found = true;
                        callbackFound(valueContainer);
                    }
                });

                if (!found) {
                    callbackNotFound();
                }
            }

            var valueCol = parseInt($(container).attr(attribute.col));
            var selectCol = 12 - valueCol;

            $(container).find(selector.value).hide();

            $(container).find(selector.select).find('select').change(function() {
                getValueContainer($(this).val(), function(newValueContainer) {
                    var 
                        showValueContainer = function() {
                            if (!$(newValueContainer).hasClass('col-sm-' + valueCol)) {
                                $(newValueContainer).addClass('col-sm-' + valueCol)
                            }

                            $(newValueContainer).fadeIn(c.fadeInDuration);
                        },
                        visibleValueContainer;

                    $(container).find(selector.select)
                        .removeClass('col-sm-12')
                        .addClass('col-sm-' + selectCol)
                        .addClass('sm-margin-bottom-10');

                    if (selectCol == 0) {
                        $(container).find(selector.select).addClass('margin-bottom-10');
                    }

                    if ($(container).find(selector.value + ':visible').length) {
                        visibleValueContainer = $(container).find(selector.value + ':visible').get(0);

                        if (visibleValueContainer != newValueContainer) {
                            $(visibleValueContainer).fadeOut(c.fadeOutDuration, showValueContainer);
                        }
                    } else {
                        showValueContainer();
                    }
                }, function() {
                    $(container).find(selector.value + ':visible').fadeOut(c.fadeOutDuration, function() {
                        $(container).find(selector.select)
                            .removeClass('col-sm-' + selectCol)
                            .addClass('col-sm-12')
                            .removeClass('sm-margin-bottom-10');

                        if (selectCol == 0) {
                            $(container).find(selector.select).removeClass('margin-bottom-10');
                        }
                    });
                });
            }).trigger('change');

            return container;
        });
    };

    $(document).ready(function() {
        $(selector.container).dimensionContainer({
            fadeOutDuration: fadeOutDuration,
            fadeInDuration: fadeInDuration
        });
    });
}(jQuery));
