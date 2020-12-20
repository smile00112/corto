<af_selected_range>
<div class="af-selected" onclick={click}><p>{opts.group[0]} - {opts.group[1]}</p></div>
<script>
    this.mixin({store: d_ajax_filter})
    click(e){
        this.store.clearSelected(this.opts.name, this.opts.group_id, this.parent.opts.id);
    }
</script>
</af_selected_range>