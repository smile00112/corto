<af_button_filter>
<a id="fitlers">{{button_filter}}</a>
<div if={store.getState().setting.button_filter_position?} class="close">
    <i class="fa fa-times-circle-o" onclick="$(this).closest('.af-button-filter').css('display','none')" aria-hidden="true"></i>
</div>
<script>
    this.mixin({store: d_ajax_filter})
</script>

</af_button_filter>