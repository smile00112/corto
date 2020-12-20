function af() {

    //allows for the af object to trigger and listen to custom events.
    riot.observable(this);

    /**
    *   initState. Initialize your app. This will add the default value to the
    * state. Refer to Redux http://redux.js.org/docs/api/Store.html
    */
    this.initState = function(state){
        if(typeof this.state == 'undefined'){
            this.state = state;
        }
        else{
            this.state = _.extend(this.state, state);
        }
        //allows the af.init(store) to be passed into the mixins model value.
        return this;
    }

    /**
    *   UpdateState. A wrapper function to update the state and call riot update.
    */
    this.updateState = function(data){

        this.state = merge(this.state, data);
        riot.update(); //will start a full update of all tags
    }

    /**
    *   GetState. Returns the state.
    */
    this.getState = function(){
        return this.state;
    }

    /**
    *   Redux:dispatch function. A wrapper function for triggering a custom event with a
    * updated state value.
    */
    this.dispatch = function(action, state){
        this.trigger(action, state);
    }

    this.setGroups = function(id, groups){
        if(typeof this.groups == 'undefined'){
            this.groups = {};
        }

        if(typeof this.status_groups == 'undefined'){
            this.status_groups = Object.keys(groups);
        }
        else{
            this.status_groups = _.union(this.status_groups, Object.keys(groups));
        }

        if(typeof this.state.groups == 'undefined'){
            this.state.groups = groups;
        }
        else{
            this.state.groups = _.extend(this.state.groups, groups);
        }

        this.groups[id] = groups;
    }

    this.getGroups = function(id){
        return this.groups[id];
    }

    this.setSetting = function(id, setting){
        if(typeof this.setting == 'undefined'){
            this.setting = {};
        }

        this.setting[id] = setting;
    }

    this.getSetting = function(id){
        return this.setting[id];
    }

    /**
    *   Redux:subscribe function. A wrapper function for catching a custom event with a
    * callback function.
    */
    this.subscribe = function(action, callback){
        this.on(action, callback);
    }

    this.setViewStatus = function(name, group_id, filter_id, status){
        if(status){
            if(typeof this.views == 'undefined'){
                this.views = {};
            }
            if(typeof this.views[filter_id] == "undefined"){
                this.views[filter_id] = {};
            }
            if(typeof this.views[filter_id][name] == "undefined"){
                this.views[filter_id][name] = [];
            }
            this.views[filter_id][name].push(group_id);
        }
        else{
            var index = this.views[filter_id][name].indexOf(group_id);
            this.views[filter_id][name].splice(index, 1);
        }
    }

    this.getViewStatus = function(name, group_id, filter_id){
        var result = false;
        if(typeof this.views != 'undefined' &&
            typeof this.views[filter_id] != 'undefined' &&
            typeof this.views[filter_id][name] != 'undefined' &&
            this.views[filter_id][name].indexOf(group_id) != -1) 
        {
            result = true;
        }
        return result;
    }

    this.getGroupCaption = function(name, group_id){
        var result = name+'_'+group_id;
        if(typeof this.getState().groups != 'undefined' &&
            typeof this.getState().groups[name] != 'undefined' &&
            typeof this.getState().groups[name][group_id] != 'undefined')
        {
            result = this.getState().groups[name][group_id].caption;
        }
        return result;
    }

    this.getElementCaption = function(name, group_id, value){
        var result = value;

        if(typeof this.getState().groups != 'undefined' &&
            typeof this.getState().groups[name] != 'undefined' &&
            typeof this.getState().groups[name]['_'+group_id] != 'undefined'&&
            typeof this.getState().groups[name]['_'+group_id].values['_'+value] != 'undefined')
        {
            result = this.getState().groups[name]['_'+group_id].values['_'+value].name;
        }
        return result;
    }

    this.updateSelected = function(name, group_id, value, checked, filter_id, target){
        filter_id = typeof filter_id !== 'undefined' ? filter_id : null;
        target = typeof target !== 'undefined' ? target : null;
        if (filter_id == 'af-selected-wrapper') {
            filter_id = _.keys(this.groups)[0]
        }
        if(checked){
            if(typeof this.state.selected[name] == "undefined"){
                this.state.selected[name] = {};
            }
            if(typeof this.state.selected[name][group_id] == "undefined"){
                this.state.selected[name][group_id] = [];
            }
            else{
                this.state.selected[name][group_id] = this.getValuesFromObject(this.state.selected[name][group_id]);
            }
            this.state.selected[name][group_id].push(value);
        }
        else{
            if(typeof this.state.selected[name] != "undefined" && typeof this.state.selected[name][group_id] != "undefined"){
                this.state.selected[name][group_id] = this.getValuesFromObject(this.state.selected[name][group_id]);

                var index = this.state.selected[name][group_id].indexOf(value);
                this.state.selected[name][group_id].splice(index, 1);
            }
        }
        riot.update();
        if(filter_id){
            if(this.getSetting(filter_id).submission == '0'){
                this.updateContent();
            }
        }
        if(target){
            var position = $(target).closest('.af-element').get(0).offsetTop;
            position += $(target).closest('.af-element').get(0).offsetHeight/2;
            $(target).closest('.ajax-filter').trigger('change-location', Math.round(position));
        }
    }

    this.updateGroupSelected = function(name, group_id, values, filter_id){
        filter_id = typeof filter_id !== 'undefined' ? filter_id : null;
        this.clearSelected(name, group_id);

        if(typeof this.state.selected[name] == "undefined"){
            this.state.selected[name] = {};
        }

        if(typeof this.state.selected[name][group_id] == "undefined"){
            this.state.selected[name][group_id] = [];
        }
        this.state.selected[name][group_id] = values;
        riot.update();
        if(filter_id){
            if(this.getSetting(filter_id).submission == '0'){
                this.updateContent();
            }
        }
    }

    this.clearSelected = function(name, group_id, filter_id, target){
        filter_id = typeof filter_id !== 'undefined' ? filter_id : null;
        target = typeof target !== 'undefined' ? target : null;

        if(typeof this.state.selected[name] != "undefined" && typeof this.state.selected[name][group_id] != "undefined"){
            delete this.state.selected[name][group_id];
        }

        if(filter_id){
            riot.update();
            if(this.getSetting(filter_id).submission == '0'){
                this.updateContent();
            }
            if(target){
                var position = $(target).closest('.af-element').get(0).offsetTop;
                position += $(target).closest('.af-element').get(0).offsetHeight/2;
                $(target).closest('.ajax-filter').trigger('change-location', Math.round(position));
            }
        }
    }

    this.clearSelectedAll = function(filter_id, target){
        filter_id = typeof filter_id !== 'undefined' ? filter_id : null;
        target = typeof target !== 'undefined' ? target : null;

        if (filter_id == 'af-selected-wrapper') {
            filter_id = _.keys(this.groups)[0]
        }
        
        this.state.selected = {};

        if(filter_id){
            riot.update();
            if(this.getSetting(filter_id).submission == '0'){
                this.updateContent();
            }
            if(target){
                var position = 0;
                if($(target).closest('.af-element') > 0){
                    position = $(target).closest('.af-element').get(0).offsetTop;
                    position += $(target).closest('.af-element').get(0).offsetHeight/2;
                }
                else{
                    position = $(target).closest('.title').get(0).offsetTop;
                    position += $(target).closest('.title').get(0).offsetHeight/2;
                }
                $(target).closest('.ajax-filter').trigger('change-location', Math.round(position));
            }
        }
    }

    this.getSelected = function(name, group_id){
        var result = [];
        if(typeof this.getState().selected != 'undefined' &&
            typeof this.getState().selected[name] != 'undefined' &&
            typeof this.getState().selected[name][group_id] != 'undefined')
        {
            result = this.getState().selected[name][group_id];
        }
        return result;
    }

    this.checkSelected = function(name, group_id, value){
        var selected = false;

        if(typeof this.getState().selected != 'undefined' &&
            typeof this.getState().selected[name] != 'undefined' &&
            typeof this.getState().selected[name][group_id] != 'undefined'&&
            this.getValuesFromObject(this.getState().selected[name][group_id]).indexOf(value.toString()) != -1)
        {
            selected = true;
        }
        return selected;
    }

    this.loadQuantity = function(id){
        if(this.setting[id].display_quantity == "1" && typeof this.quantity_status == 'undefined'){
            this.quantity_status = true;

            var send_data = {
                'status':this.status_groups
            }
            send_data = _.extend(send_data, this.state.selected);

            $.ajax({
                context: this,
                url: this.state.url.quantity,
                dataType: 'json',
                type: 'post',
                data: send_data,
                success: function(json) {
                    if (json['success']) {
                        this.updateQuantity(json['quantity']);
                    }
                }
            });
        }
    },

    this.updateQuantity = function(quantity){
        this.state.quantity = quantity;
        riot.update();
    }

    this.getQuantity = function(name, group_id, value){
        var result = 0;
        if(typeof this.getState().quantity != 'undefined' &&
            typeof this.getState().quantity[name] != 'undefined' &&
            typeof this.getState().quantity[name][group_id] != 'undefined' && 
            typeof this.getState().quantity[name][group_id][value] != 'undefined')
        {
            result = this.getState().quantity[name][group_id][value];
        }
        return result;
    }

    this.checkDisabled = function(name, group_id, value, filter_id){

        if(this.setting[filter_id].display_quantity == "0"){
            return false;
        }
        return this.getQuantity(name, group_id, value) == 0;
    }

    this.displayQuantity = function(name, group_id, value, filter_id){
        selected = this.checkSelected(name, group_id, value);
        if(selected){
            return false;
        }
        if(this.setting[filter_id].display_quantity == "0"){
            return false;
        }
        else{
            return !this.checkDisabled(name, group_id, value, filter_id);
        }
    }

    this.checkRange = function(name, group_id){
        var result = false;

        if(typeof this.getState().groups != 'undefined' &&
            typeof this.getState().groups[name] != 'undefined' &&
            typeof this.getState().groups[name]['_'+group_id] != 'undefined'&&
            this.getState().groups[name]['_'+group_id].mode == 'range')
        {
            result = true;
        }
        return result;
    }

    this.getValuesFromObject = function(obj){
        return Object.keys(obj).map(function(key) {
            return obj[key];
        });
    }

    this.updateContent = function(){
        var send_data = {
            'status':this.status_groups,
            'quantity_status': typeof this.quantity_status != 'undefined'?1:0
        }
        send_data = _.extend(send_data, this.state.selected);

        if(this.beforeRequest != null){
            this.beforeRequest();
        }

        $.ajax({
            context: this,
            url: this.state.url.ajax,
            dataType: 'json',
            type: 'post',
            data: send_data,
            beforeSend: function() {
                if(this.getState().common_setting.display_loader == '1'){

                    if (this.getState().common_setting.content_path != '') {
                        if($(this.getState().common_setting.content_path).size() > 0){
                            var top = $(this.getState().common_setting.content_path).offset().top+150;
                            $(this.getState().common_setting.content_path).append('<af_loader style="top:'+top+'px;"></af_loader>');
                        }
                        else{
                            console.log('Ajax Filter: content path not found');
                        }
                    }

                    riot.mount('af_loader');
                }
                if(this.getState().common_setting.fade_out_product == '1'){
                    if (this.getState().common_setting.content_path != '') {
                        $(this.getState().common_setting.content_path + " > :not(af_loader)").fadeTo('slow', 0.5);
                    }
                    
                }
            },
            success: function(json) {
                if (json['success']) {
                    this.processData(json);
                }
            }
        });
    }

    this.processData = function(json){
        if(this.beforeRender != null){
            this.beforeRender(json);
        }

        var protocol = window.location.protocol;

        var url = decodeURIComponent(json['url']);

        url = url.replace(/(http|https):/, protocol);
        if (this.getState().common_setting.ajax == 1) {

            window.history.pushState("object or string", "Title", url);

            if (this.getState().common_setting.content_path != '') {
                $(this.getState().common_setting.content_path).html($(json['products']).find(this.getState().common_setting.content_path).html());
            }

            if (json['quantity']) {
                this.updateQuantity(json['quantity']);
            }
            $('#list-view').click(function() {
                $('#content .product-grid > .clearfix').remove();

                $('#content .row > .product-grid').attr('class', 'product-layout product-list col-xs-12');

                localStorage.setItem('display', 'list');
            });


            $('#grid-view').click(function() {
                $('#content .product-layout > .clearfix').remove();
                var cols = $('#column-right, #column-left').length;

                if (cols == 2) {
                    $('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
                } else if (cols == 1) {
                    $('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
                } else {
                    $('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
                }

                localStorage.setItem('display', 'grid');
            });
            if (this.getState().common_setting.display_selected_top == '1') {
                if($('d_ajax_filter_selected').size() == 0) {
                    $(this.getState().common_setting.selected_path).before('<d_ajax_filter_selected id="af-selected-wrapper" class="empty-wrapper"></d_ajax_filter_selected>')
                    riot.mount(document.getElementById('af-selected-wrapper'))
                }
            }

            if (localStorage.getItem('display') == 'list') {
                $('#list-view').trigger('click');
            } else {
                $('#grid-view').trigger('click');
            }
        } else {
            window.location.href = url;
        }

        if(this.afterRender != null){
            this.afterRender(json);
        }
    }
}

/**
 *  Alias for d_ajax_filter
 */
 var d_ajax_filter = new af();