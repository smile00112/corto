<af_button_view_all>
<div class="af-show-all" if={count_hide_elements >= store.getSetting(opts.filter_id).min_elemnts} onclick={click}>
    {store.getViewStatus(opts.filter.name, this.parent.opts.group_id, opts.filter_id)?store.getState().translate.text_shrink:store.getState().translate.text_show_more}
</div>
<script>
    this.mixin({store: d_ajax_filter})
    this.on('before-mount', function(){
        count_hide_elements = this.store.getValuesFromObject(opts.filter.values).length - this.store.getSetting(opts.filter_id).count_elemnts;
    });
    this.on('mount', function(){
        $(this.root).parent().find('.af-element:nth-child(n+'+(parseInt(this.store.getSetting(opts.filter_id).count_elemnts)+1)+')').css('display','none');
    });
    this.on('update', function(){
        count_hide_elements = this.store.getValuesFromObject(opts.filter.values).length - this.store.getSetting(opts.filter_id).count_elemnts;
    });
    
    click(e){
        if(!this.store.getViewStatus(opts.filter.name, this.parent.opts.group_id, opts.filter_id)){
            $(this.root).parent().find('.af-element:nth-child(n+'+(parseInt(this.store.getSetting(opts.filter_id).count_elemnts)+1)+')').css('display','flex');
            this.store.setViewStatus(opts.filter.name, this.parent.opts.group_id, opts.filter_id, true);
        }
        else{
            $(this.root).parent().find('.af-element:nth-child(n+'+(parseInt(this.store.getSetting(opts.filter_id).count_elemnts)+1)+')').css('display','none');
            this.store.setViewStatus(opts.filter.name, this.parent.opts.group_id, opts.filter_id, false);
        }
    }
</script>
</af_button_view_all>