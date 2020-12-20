(function($) {
    var 
        form_html = `
            <form enctype="multipart/form-data" role="image-upload-form" style="display: none;">
                <input type="file" name="file" />
            </form>
        `,
        selector = {
            container: '[role="image-upload"]',
            preview: '[role="image-upload-preview"]',
            button: '[role="image-upload-button"]',
            hidden: '[role="image-upload-hidden"]',
            form: '[role="image-upload-form"]'
        },
        attribute = {
            url: 'data-image-upload-url',
            value: 'data-image-upload-value'
        },
        fadeOutDuration = 150,
        fadeInDuration = 300;

    $.fn.imageUpload = function(c) {
        return this.each(function(index, container) {
            $(container).find(selector.button).click(function(e) {
                e.preventDefault();
                e.stopPropagation();

                var
                    form = $(form_html),
                    button = this;

                $('body')
                    .find(selector.form).remove()
                    .prepend(form);

                $(form).find('input[name="file"]')
                    .change(function() {
                        $.ajax({
                            url: $(container).attr(attribute.url),
                            type: 'post',
                            dataType: 'json',
                            data: new FormData($(form).get(0)),
                            cache: false,
                            contentType: false,
                            processData: false,
                            beforeSend: function() {
                                $(button).button('loading');
                            },
                            complete: function() {
                                $(button).button('reset');
                            },
                            success: function(json) {
                                if (json.error) {
                                    alert(json.error);
                                }

                                if (json.success) {
                                    alert(json.success);

                                    $(container).find(selector.hidden).val(json.image.file).trigger('change');
                                    $(container).find(selector.preview).html(`<img src="${json.image.url}" alt="" />`);
                                }
                            },
                            error: function(xhr, ajaxOptions, thrownError) {
                                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                            }
                        });
                    })
                    .trigger('click');
            });

            return container;
        });
    }

    $(document).ready(function() {
        $(selector.container).imageUpload({
            fadeOutDuration: fadeOutDuration,
            fadeInDuration: fadeInDuration,
            value: null
        });
    });
}(jQuery));
