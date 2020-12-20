<af_group_slider_label>
<div class="af-element slider">
    <input type="hidden" data-type="min" data-mode="slider" name="{opts.filter.name}[{opts.filter.group_id}][]" value="{opts.filter.current_min}">
    <input type="hidden" data-type="max" data-mode="slider"  name="{opts.filter.name}[{opts.filter.group_id}][]" value="{opts.filter.current_max}">
    <div id="slider-range" class="slider-range slider-label"></div>
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
            hide_min_max: false,
            hide_from_to: false,
            onFinish: function (data) {
                this.store.updateGroupSelected(opts.filter.name, opts.filter.group_id, [data.from, data.to], opts.filter_id);
            }.bind(this)
        });
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
</script>
<script>
    this.on('mount', function(){
        var range = document.getElementById('slider-range');
        
    })
</script>
</af_group_slider_label>