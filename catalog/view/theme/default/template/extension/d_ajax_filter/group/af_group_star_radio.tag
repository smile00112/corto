<af_group_star_radio>
<div each={element in opts.filter.values} opts={element} class="af-element rating {store.checkDisabled(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id) ?'disabled':''}">
    <label>
        <input type="radio" name="{parent.opts.filter.name}[{parent.opts.filter.group_id}][]" checked={parent.store.checkSelected(parent.opts.filter.name, parent.opts.filter.group_id, element.value)} value="{element.value}" onchange={change}><span></span>
        <input type="hidden" class="rating" value="{element.value}" data-readonly/>
        <div class="title"><af_quantity if={parent.store.displayQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id)} quantity={parent.store.getQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value)}></af_quantity></div>
    </label>
</div>
<af_button_view_all filter_id={opts.filter_id} filter={opts.filter} if={store.getSetting(opts.filter_id).limit_block == '1'}></af_button_view_all>
<script>
    this.mixin({store: d_ajax_filter})
    this.on('mount', function(){
        $('input[type=hidden].rating', this.root).rating({
            filled: 'fa fa-star',
            empty: 'fa fa-star-o'
        });
    })
    change(e){
        this.store.clearSelected(opts.filter.name, opts.filter.group_id);
        this.store.updateSelected(opts.filter.name, opts.filter.group_id, e.target.value, e.target.checked, opts.filter_id, e.target);
    }
</script>
</af_group_star_radio>