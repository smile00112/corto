<af_group_radio>
<div each={element in opts.filter.values}  class="af-element {store.checkDisabled(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id)?'disabled':''}">
    <label>
        <input type="radio" name="{parent.opts.filter.name}[{parent.opts.filter.group_id}]" checked={store.checkSelected(parent.opts.filter.name, parent.opts.filter.group_id, element.value)} value="{element.value}" onchange={change}><span></span> 
        <div class="title">{element.name} <af_quantity if={store.displayQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id)} quantity={store.getQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value)}></af_quantity></div>
    </label>
    
</div>
<af_button_view_all filter_id={opts.filter_id} filter={opts.filter} if={store.getSetting(opts.filter_id).limit_block == '1'}></af_button_view_all>
<script>
    this.mixin({store: d_ajax_filter})
    change(e){
        this.store.clearSelected(opts.filter.name, opts.filter.group_id);
        this.store.updateSelected(opts.filter.name, opts.filter.group_id, e.target.value, e.target.checked, opts.filter_id, e.target);
    }
</script>
</af_group_radio>