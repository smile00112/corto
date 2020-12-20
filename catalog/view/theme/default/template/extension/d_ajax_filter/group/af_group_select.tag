<af_group_select>
<div class="af-element">
    <div class="af-select-box">
        <div  class="af-select-element empty" onclick={handler} data-value="*">
            <p>{store.getState().translate.text_none}</p>
        </div>
        <virtual each={element in opts.filter.values}>
            <div class="af-select-element {store.checkSelected(parent.opts.filter.name, parent.opts.filter.group_id, element.value)? 'active':''} {store.checkDisabled(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id) ?'disabled':''}" onclick={handler} data-value="{element.value}">
                <p>{element.name}<af_quantity if={store.displayQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value, parent.opts.filter_id)}  quantity={store.getQuantity(parent.opts.filter.name, parent.opts.filter.group_id, element.value)}></af_quantity></p>
            </div>
        </virtual>
    </div>
</div>
<script>
    this.mixin({store: d_ajax_filter})
    this.on('mount', function(){
        $('.af-select-box:not(.in) > .af-select-element.active', this.root).prevAll('.empty').hide();
    })
    handler(e){
        if($(e.target).closest('.af-select-box').hasClass('in')){
            if(e.currentTarget.dataset.value != '*'){
                $('.af-select-element.empty', this.root).hide();
                this.store.clearSelected(opts.filter.name, opts.filter.group_id);
                this.store.updateSelected(opts.filter.name, opts.filter.group_id, e.currentTarget.dataset.value, 1, opts.filter_id, e.target);
            }
            else{
                this.store.clearSelected(opts.filter.name, opts.filter.group_id, opts.filter_id);
            }
            $(e.target).closest('.af-select-box').removeClass('in');
        }
        else{
            $('.af-select-element.empty', this.root).show();
            $(e.target).closest('.af-select-box').addClass('in');
        }
    }
</script>
</af_group_select>