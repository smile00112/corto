<af_group_slider_inputs>
<div class="af-element slider">
    <div class="slider-range"></div>
    <div class="price">
        <div class="input-price">
            <i>{store.getState().translate.text_symbol_left}</i>
            <input class="price-min-input" type="text" name="{opts.filter.name}[{opts.filter.group_id}][]" onchange="{change}">
            <i>{store.getState().translate.text_symbol_right}</i>
        </div>
        <span class="slider-separator"></span>
        <div class="input-price">
            <i>{store.getState().translate.text_symbol_left}</i>
            <input class="price-max-input" type="text" name="{opts.filter.name}[{opts.filter.group_id}][]" onchange="{change}">
            <i>{store.getState().translate.text_symbol_right}</i>
        </div>
        
    </div>
</div>
<script>
    this.mixin({store: d_ajax_filter})

    this.on('mount', function(){

        var values = this.store.getSelected(opts.filter.name, opts.filter.group_id);

        if(values.length == 0){
            values = opts.filter.values;
        }

        $(".slider-range").ionRangeSlider({
            type: "double",
            min: opts.filter.values[0],
            max: opts.filter.values[1],
            from: values[0],
            to: values[1],
            grid: false,
            hide_min_max: true,
            hide_from_to: true,
            onChange: function (data) {
                $('.price-min-input', this.root).val(data.from);
                $('.price-max-input', this.root).val(data.to);
            }.bind(this),
            onFinish: function (data) {
                $('.price-min-input', this.root).val(data.from);
                $('.price-max-input', this.root).val(data.to);
                this.store.updateGroupSelected(opts.filter.name, opts.filter.group_id, [data.from, data.to], opts.filter_id);
            }.bind(this)
        });

        $('.price-min-input', this.root).val(values[0]);
        $('.price-max-input', this.root).val(values[1]);
    })

    this.on('update', function(){

        var values = this.store.getSelected(opts.filter.name, opts.filter.group_id);

        var slider = $(".slider-range", this.root).data("ionRangeSlider");

        if(typeof this.store.quantity_status != 'undefined'){

            var min = this.store.getQuantity(opts.filter.name, opts.filter.group_id, 0);
            var max = this.store.getQuantity(opts.filter.name, opts.filter.group_id, 1);
            

            var current_min = slider.options.min;
            var current_max = slider.options.max;

            if(slider.options.min != min){
                current_min = min;
            }
            if(slider.options.max != max){
                current_max = max;
            }

            slider.update({
                min: current_min,
                max: current_max
            });

            if(typeof values == "object"){
                var length = this.store.getValuesFromObject(values).length;
            }
            else{
                var length = values.length;
            }

            if(length > 0){
                if(values[0] < current_min){
                    slider.update({
                        from: current_min
                    });
                    $('.price-min-input', this.root).val(current_min);
                }

                if(values[1] > current_max){
                    slider.update({
                        to: current_max
                    });
                    $('.price-max-input', this.root).val(current_max);
                }

                user_min = values[0];
                user_max = values[1];

                if(user_min < current_min){
                    user_min = current_min;
                }

                if(user_max > current_max){
                    user_max = current_max;
                }

                slider.update({
                    from: user_min,
                    to: user_max
                });
                
            }
            else{
                $('.price-min-input', this.root).val(current_min);
                $('.price-max-input', this.root).val(current_max);
                slider.update({
                    from: current_min,
                    to: current_max
                });
            }
            
        }
        else{
            if(values.length == 0){
                values = opts.filter.values;
            }

            slider.update({
                from: values[0],
                to: values[1]
            });

            $('.price-min-input', this.root).val(values[0]);
            $('.price-max-input', this.root).val(values[1]);
        }
    });

    change(e){
        var min_price = $('.price-min-input', this.root).val();
        var max_price = $('.price-max-input', this.root).val();

        this.store.updateGroupSelected(opts.filter.name, opts.filter.group_id, [min_price, max_price], opts.filter_id);
    }
</script>
</af_group_slider_inputs>