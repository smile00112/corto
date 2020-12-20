(function($) {
    var 
        selector = {
            select: '[role="font-select"]',
            button: '[role="button"]',
            menu: '.bootstrap-select.btn-group .dropdown-menu.inner',
            index: '[data-original-index="{index}"] a .text',
            option: '.bootstrap-select.btn-group .dropdown-toggle .filter-option'
        };

    $.fn.fontSelect = function(c) {
        var id = $(selector.select).attr('id');
        var select_element = this;
        var applyFont = function() {
            var font_family = $(select_element).find('option[value="' + $(select_element).val() + '"]').text();

            $(selector.button + '[data-id="' + id + '"]')
                .css({
                    'font-family' : font_family
                });
        }

        $(select_element)
            .on('changed.bs.select', applyFont)
            .on('rendered.bs.select', function() {
                $(select_element).find('option').each(function(index, element) {
                    $(selector.menu).find(selector.index.replace('{index}', index)).css(
                        'font-family',
                        $(element).text()
                    );
                });

                applyFont();
            });

        return this;
    }

    $(document).ready(function() {
        $(selector.select).fontSelect();
    });
}(jQuery));
