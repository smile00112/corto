<af_selected>
    <div class="af-selected" onclick={click}><p>{store.getElementCaption(parent.name, parent.group_id, value)}</p></div>
    <script>
        this.mixin({store: d_ajax_filter})
        click(e){
            this.store.updateSelected(this.parent.name, this.parent.group_id, this.value, 0, this.parent.parent.parent.opts.id);
        }
    </script>
</af_selected>