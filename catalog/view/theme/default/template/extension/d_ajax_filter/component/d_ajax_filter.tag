<d_ajax_filter>

<div class="title">
    <div class="title">
        <div class="button-reset" if={store.getSetting(opts.id).button_reset == '1'} id="resetFilter" onclick={click}>
            <span></span><p>{store.getState().translate.button_reset}</p>
        </div>
        {store.getSetting(opts.id).heading_title}
    </div>
</div>
<div class="af-body">
    <div class="selected-list clearfix" if={store.getSetting(opts.id).selected_filters == '1'}>
        <div each={groups, name in store.getState().selected}>
            <div each={group, group_id in groups}>
                <af_selected each={value in group} if={!store.checkRange(parent.name, group_id)}></af_selected>
                <af_selected_range group={group} name={parent.name} group_id={group_id} if={store.checkRange(parent.name, group_id)}></af_selected>
            </div>
        </div>
    </div>
    
    

    <virtual each={groups, name in store.getGroups(opts.id)}>
        <af_group each={filter in groups} filter_id="{parent.parent.opts.id}"></af_group>
    </virtual>
    <div if={store.getSetting(opts.id).submission == '1'} class="af-button-filter {store.getSetting(opts.id).button_filter_position == 1?'af-popup':''}">
        <a id="fitlers" onclick={filter_click}>{store.getState().translate.button_filter}</a>
        <div if={store.getSetting(opts.id).button_filter_position == 1} class="close">
            <i class="fa fa-times-circle-o" onclick="$(this).closest('.af-button-filter').css('display','none')" aria-hidden="true"></i>
        </div>
    </div>
</div>
<script>
    this.mixin({store: d_ajax_filter})
    click(e){
        this.store.clearSelectedAll(opts.id, e.target);
    }

    filter_click(e){
        this.store.updateContent();
    }

    if(this.store.getSetting(opts.id).submission == '1' && this.store.getSetting(opts.id).button_filter_position == '1'){
        $(this.root).on('change-location', function(e, top){
            var position_top = top - Math.round($('.af-button-filter.af-popup', this.root).outerHeight(true) / 2); 
            $('.af-button-filter.af-popup', this.root).css({top:position_top});
            $('.af-button-filter.af-popup', this.root).show();
            clearTimeout(timeout_popup);
            var timeout_popup = setTimeout(function(){$('.af-button-filter.af-popup', this.root).hide();}, 2000);
        }.bind(this));
    }
    
</script>
</d_ajax_filter>