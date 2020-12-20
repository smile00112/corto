<af_group>
<div class="af-container">
    <div class="af-heading af-collapse" data-toggle="collapse" data-target="#{filter.name}_{filter.group_id}_{opts.filter_id}" aria-expanded="true">
        <p class="title">{filter.caption}</p><span></span>
    </div>
    <div id="{filter.name}_{filter.group_id}_{opts.filter_id}"  class="af-elements collapse {filter.collapse == '0'?'in':''}" aria-expanded="true">
        <div class="af-wrapper">
            <div data-is='af_group_{filter.type}' filter={filter} filter_id={opts.filter_id}></div>
        </div>
    </div>
</div>
<script>
    this.mixin({store: d_ajax_filter})
    this.on('mount', function(){
        var setting = this.store.getSetting(opts.filter_id)
        $(".af-elements", this.root).mCustomScrollbar({
            axis:"y",
            theme: setting.theme_scrollbar
        });
    })
</script>
</af_group>