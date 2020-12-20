var d_ajax_filter = {
    //Настройки
    setting:{
        //текущая форма
        form: '',
        //Базовый url
        url:'index.php?route=extension/d_ajax_filter/filter',
        //token
        token:''
    },
    //Шаблоны
    template: {
        //шаблон колонки
        element: '',
        //Шаблон тегов option для Select
        options:'',
        //Шаблон изображений фильтров
        filter_images:'',
        //Шаблон менеджера изображений
        filemanager:''
    },
    //Инициализация
    init: function(setting){
        this.setting = $.extend({}, this.setting, setting);
        this.initPartial();
    },
    //Инициализация шаблонов
    initTemplate: function(template) {
        this.template = $.extend({}, this.template, template);
    },
    //Инициализация Handlebars Partial
    initPartial: function() {
        if (window.Handlebars !== undefined) {
            console.log('d_visual_designer:init_partials');
            window.Handlebars.registerHelper('select', function(value, options) {
                var $el = $('<select />').html(options.fn(this));
                $el.find('[value="' + value + '"]').attr({ 'selected': 'selected' });
                return $el.html();
            });
            window.Handlebars.registerHelper('concat', function(value, options) {
                var res = [];
                for (var key in value) {
                    res.push(value[key]['setting']['size']);
                }
                return res.join(options['hash']['chart']);
            });
            window.Handlebars.registerHelper('ifCond', function(v1, v2, options) {
                if (v1 === v2) {
                    return options.fn(this);
                }
                return options.inverse(this);
            });
        }

    },
    //Создание Sortable
    createSortable:function(selector,child){

        tinysort(selector+' > '+child, {selector:'input.sort-value',useVal:true});

        var that = this;
        Sortable.create($(selector)[0], {
            animation: 100,
            sort: true,
            onUpdate: function (ev){
                that.setting.form.find(selector+' > '+child).each(function (i, row) {
                    $(row).find('.sort-value').val(i)
                });
                that.setting.form.find('#attribute > #saveValues').show();
            }
        });
    },
    //Обновление Sort Order
    updateSortOrder:function(selector, child){
        this.setting.form.find(selector+' > '+child).each(function (i, row) {
            $(row).find('.sort-value').val(i)
        });
    },
    //Сохранение настроек аттрибутов
    save:function(){
        var that = this;
        $.ajax({
            url:that.setting.form.attr('action'),
            type:'post',
            dataType:'json',
            data:that.setting.form.find("#d_list_filter").find('input[name^=d_ajax_filter_filters], select[name^=d_ajax_filter_filters]').serializeJSON(),
            beforeSend:function(){
                that.setting.form.fadeTo('slow', 0.5);
            },
            success:function(json){
                that.setting.form.find('.form-group.has-error').removeClass('has-error');
                that.setting.form.find('.form-group .text-danger').remove();
                $('.alert').remove();
                if(json['success']){
                    location.href = json['redirect'];
                }
                if(json['error']){
                    for (var key in json['errors']){
                        that.setting.form.find('[data-error="'+key+'"]').after('<div class="text-danger">'+json['errors'][key]+'</div>');
                        that.setting.form.find('[data-error="'+key+'"]').closest('.form-group').addClass('has-error');
                    }
                    $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }
            },
            complete:function(){
                that.setting.form.fadeTo('slow', 1);
            }
        });
    },
    addFilter:function(filter_group_id, filter_name){
        var data = {
            id:filter_group_id,
            name:filter_name,
            key:'filters'
        };
        var content = this.templateСompile(this.template.new_element, data);
        if(this.setting.form.find('.table-filter-select > tbody > tr#element-filters-'+filter_group_id).length == 0){
            this.setting.form.find('.table-filter-select > tbody').append(content);
        }
    },
    //Компиляция шаблона
    templateСompile: function(template, data) {
        var source = template.html();
        var template = _.template(source);
        var html = template(data);
        return html;
    },
    //Отрисовка аттрибутов
    renderFilterGroups:function(language_id, target){

        var that = this;
        $.ajax({
            type:'post',
            url:that.setting.url+'/getFilterGroups&'+that.setting.token,
            data: {language_id:language_id},
            dataType: 'json',
            success:function(json){
                var content = that.templateСompile(that.template.options,{'values':json['values']});
                that.setting.form.find(target).find('option[value!="*"]').remove();
                that.setting.form.find(target).append(content);
            }
        });
    },
    //Отрисовка изображений фильтров
    renderFilterImages:function(filter_group_id, language_id){

        var that = this;
        $.ajax({
          type:'post',
          url:that.setting.url+'/getFilterImages&'+that.setting.token,
          data: {filter_group_id:filter_group_id,language_id:language_id},
          dataType: 'json',
          success:function(json){
            if(json['success']){
                var content = that.templateСompile(that.template.filter_images,{'values':json['values']});
                that.setting.form.find("div#filter_images").html(content);
                that.updateFileManager();
                if(Object.keys(json['values']).length > 0){
                    that.setting.form.find('#filter_group_image > #saveFilterImages').show();
                    that.setting.form.find('#filter_group_image > #reset_image_filter_group').show();
                }
                else{
                    that.setting.form.find('#filter_group_image > #saveFilterImages').hide();
                    that.setting.form.find('#filter_group_image > #reset_image_filter_group').hide();
                }
            }
        }
    });
    },
    //Очистка изображений фильтров
    clearFilterImages:function(){
        this.setting.form.find('div#filter_images').html('');
        this.setting.form.find('#filter_group_image > #saveFilterImages').hide();
        this.setting.form.find('#filter_group_image > #reset_image_filter_group').hide();
    },

    //Сохранения порядка фильтров
    saveFitlerImages:function(language_id){
        var that = this;
        $.ajax({
            type:'post',
            url:that.setting.url+'/editFitlerImages&language_id='+language_id+'&'+that.setting.token,
            data: $("div#filter_images input").serializeJSON(),
            dataType: 'json',
            beforeSend:function(){
                that.setting.form.find('a#saveFilterImages').button('loading');
            },
            complete:function(){
               that.setting.form.find('a#saveFilterImages').button('reset');
           }
       });
    },

    //Сброс изображений значений фильтров
    resetImageFilter:function(){
        $('#filter_images .img-thumbnail').each(function(){
            $(this).attr('src', $(this).data('placeholder'));
            $(this).prev().val('');
        });
    },

    updateFileManager:function() {
        var that = this;
        this.setting.form.find('.img-thumbnail').on('click', function (e) {
            that.uploadImage($(this).prev().attr("id"), $(this).attr("id"));
            e.stopPropagation();
        }); 
        
        this.setting.form.find('.delete-image').on('click', function(e){
            $(this).prev().prev().val("");
            $(this).prev().attr("src", that.setting.placeholder);
            e.stopPropagation();
        });
    },
    uploadImage:function(field, thumb) {
        $('#modal-image').remove();
        var content = this.templateСompile(this.template.filemanager,{field:field, thumb:thumb});

        $('body').append(content);

        $('#modal-image').modal('show');
        $('.modal-backdrop').remove();
    }
}