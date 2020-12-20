<d_ajax_filter_selected class={empty && 'empty-wrapper'} if={empty}>
<div class="selected-list clearfix">
    <div each={groups, name in store.getState().selected}>
        <div each={group, group_id in groups}>
            <af_selected each={value in group} if={!store.checkRange(parent.name, group_id)}></af_selected>
            <af_selected_range group={group} name={parent.name} group_id={group_id} if={store.checkRange(parent.name, group_id)}></af_selected>
        </div>
    </div>
    <div class="button-reset" id="resetFilter" onclick={click}>
        <span></span><p>{store.getState().translate.button_reset}</p>
    </div>
</div>
<script>
    this.mixin({store: d_ajax_filter})

    if (_.isEmpty(this.store.getState().selected)) {
        this.empty = true
    } else {
        var result = true
        _.each(this.store.getState().selected, function (value) {
            if(!_.isEmpty(value[0])){
                result = false
            }
        }.bind(this))

        this.empty = result
    }

    click(e){
        this.store.clearSelectedAll(this.opts.id);
    }
    this.on('update', function(){
        this.empty = false
        if (_.isEmpty(this.store.getState().selected)) {
            this.empty = true
        } else {
            var result = true
            _.each(this.store.getState().selected, function (value) {
                if(!_.isEmpty(value[0])){
                    result = false
                }
            }.bind(this))

            this.empty = result
        }
    })
</script>
</d_ajax_filter_selected>