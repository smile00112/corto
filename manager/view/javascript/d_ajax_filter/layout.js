var d_ajax_filter = {
    //Настройки
    setting:{
        //текущая форма
        form: '',
        //Базовый url
        url:'index.php?route=extension/d_ajax_filter/layout',
    },
    //Шаблоны
    template: {
        //шаблон колонки
        element: ''
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
            window.Handlebars.registerHelper('ifChecked', function(v1, v2, options) {
                var $el = $('<div></div>').html(options.fn(this));
                if(v1 == v2){
                    $el.find('input').attr('checked', 'checked');
                }
                return $el.html();
            });

        }

    },
    //Создание Sortable
    createSortable:function(selector,child){
        if($(selector).size() > 0){
            $(selector+' > '+child).tsort({attr:'data-sort-order'});
            var that = this;
            Sortable.create($(selector)[0], {
                animation: 100,
                sort: true,
                onUpdate: function (ev){
                    that.setting.form.find(selector+' > '+child).each(function (i, row) {
                        $(row).find('.sort-value').val(i)
                    });
                }
            });
        }
        
    },
    //Обновление Sort Order
    updateSortOrder:function(selector, child){
        this.setting.form.find(selector+' > '+child).each(function (i, row) {
            $(row).find('.sort-value').val(i)
        });
    },
    //Создание модуля
    create: function(layoutSetup){
        layoutSetup = typeof layoutSetup === 'undefined'? false: layoutSetup
        var that = this;
        window.onbeforeunload = null;
        var url = '';
        if(layoutSetup){
            var url = '&layout_setting=1&status_setup=1';
        }
        $.ajax({
            url:that.setting.url+'/quickInstall&'+that.setting.token+url,
            type:'post',
            dataType:'json',
            data:that.setting.form.serializeJSON(),
            beforeSend:function(){
                that.setting.form.fadeTo('slow', 0.5);
            },
            success:function(json){
                that.setting.form.find('.form-group.has-error').removeClass('has-error');
                that.setting.form.find('.form-group .text-danger').remove();

                

                if(json['success']){
                    location.href = json['redirect'];
                }
                if(json['error']){
                    for (var key in json['errors']){
                        that.setting.form.find('[data-error="'+key+'"]').after('<div class="text-danger">'+json['errors'][key]+'</div>');
                        that.setting.form.find('[data-error="'+key+'"]').closest('.form-group').addClass('has-error');
                    }
                    if($('#content > .container-fluid > .alert').size() > 0){
                        $('#content > .container-fluid > .alert').html('<i class="fa fa-exclamation-circle"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button>')
                    } 
                    else{
                        $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                    
                }
                else{
                    $('#content > .container-fluid > .alert').remove();
                }
            },
            complete:function(){
                that.setting.form.fadeTo('slow', 1);
            }
        });
    },
    //Сохранение модуля
    save:function(){
        var that = this;
        window.onbeforeunload = null;
        $.ajax({
            url:that.setting.form.attr('action'),
            type:'post',
            dataType:'json',
            data:that.setting.form.serializeJSON(),
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
                    if($('#content > .container-fluid > .alert').size() > 0){
                        $('#content > .container-fluid > .alert').html('<i class="fa fa-exclamation-circle"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button>')
                    } 
                    else{
                        $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }
                    
                }
                else{
                    $('#content > .container-fluid > .alert').remove();
                }
            },
            complete:function(){
                that.setting.form.fadeTo('slow', 1);
            }
        });
    },
    //Удаление модуля
    delete:function(module_id){
        var that = this;
        var url = '';
        if(this.getURLVar('module_id') != ''){
            url += '&module_id='+this.getURLVar('module_id');
        }
        $.ajax({
            url:that.setting.url+'/delete&'+that.setting.token+url,
            type:'post',
            dataType:'json',
            data:{module_id:module_id},
            beforeSend:function(){
                that.setting.form.fadeTo('slow', 0.5);
            },
            success:function(json){
                if(json['success']){
                    location.href=json['redirect'];
                }
            },
            complete:function(){
                that.setting.form.fadeTo('slow', 1);
            }
        });
    },
    addElement:function(group_id, name, type, selector_main, selector_item, setting, base_types){
        var data = {
            id:group_id,
            name:name,
            key:type,
            base_types:base_types,
            setting: setting,
        };
        
        var content = this.templateСompile(this.template.new_element, data);
        if(this.setting.form.find(selector_main+' > '+selector_item+'#element-'+type+'-'+group_id).length == 0){
            this.setting.form.find(selector_main).append(content);
        }
    },
    //Компиляция шаблона
    templateСompile: function(template, data) {
        var source = template.html();
        var template = _.template(source);
        var html = template(data);
        return html;
    },

    //отрисовка настроек атрибута 
    renderAttributeSettings:function(attribute_group_id){
        var separator = this.getSeparator();
        
        var data = {};
        data['attribute_group_id'] = attribute_group_id;
        if(separator){
            data['separator'] = separator;
        }
        var that = this;
        $.ajax({
            type:'post',
            url:that.setting.url+'/getAttributeSettings&'+this.setting.token+'&filter_id='+that.setting.filter_id,
            data: data,
            dataType: 'json',
            success:function(json){
                var data = {
                    'values':json['values'],
                    'type': 'attribute',
                    'group_id': attribute_group_id
                };
                var content = that.getTemplate(that.template.filterSettings,data);
                that.setting.form.find("table#attributeTable > tbody#fitlers").html(content);
                that.renderPagination('#tab_attributes  .pagination', '#attributeTable');
            }
        });
    },

    getURLVar:function(key) {
        var value = [];

        var query = String(document.location).split('?');

        if (query[1]) {
            var part = query[1].split('&');

            for (i = 0; i < part.length; i++) {
                var data = part[i].split('=');

                if (data[0] && data[1]) {
                    value[data[0]] = data[1];
                }
            }

            if (value[key]) {
                return value[key];
            } else {
                return '';
            }
        }
    }

}