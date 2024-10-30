jQuery(document).ready(function($) {
    var plugin = $('.blrt-embed-plugin');
    var url_holder = plugin.find('#blrt-embed-link-input');
    var data = [];
    var list = plugin.find('#blrt-embed-url-placeholder');
    var message = plugin.find('.message-add-new-link');
    var add = plugin.find('.button-add-new-link');
    var publish = plugin.find('input[name="publish"]');
    $('input[name="master-checkbox"]').click(function(){
        if ($(this).is(':checked')) {
            $('input[name="checkbox"]').attr('checked', true);
        } else {
            $('input[name="checkbox"]').attr('checked', false);
        }
    })

    var array_url = plugin.find('#blrt-embed-url-placeholder input[name="url"]');
    add.on('click', add_new_url);
    
    publish.on('click', function(){
        var text = '';
        var url_list = list.find('.blrt-wp-url-single .blrt-link');
        var title_list = list.find('.blrt-wp-url-single h4')
        var fallback_list = list.find('.blrt-wp-url-single .fallback-link');
        for(var i =0; i< url_list.length; i++){
            text += $(url_list[i]).attr('href') + '+'+ $(title_list[i]).text() + '+' +  $(fallback_list[i]).attr('href') +',';
        }
        
        array_url.val(text);
    });

    function add_new_url(){
        var max = $('.blrt-wp-url-single');
        if(max.length >= 20){
            alert('Max Blrt for a gallery is 20.');
            
        }else{
            var spinner = plugin.find('.container-add-new-gallery .spinner');
            var val = url_holder.val();
            if(val != ''){
                var index = val.indexOf('/blrt/');
                if(index < 0){
                    alert('Please ensure you are using an individual Blrt link, not a Blrt Conversation link.');
                }
                else{
                    index = index + ('/blrt/').length;
                    var id = val.substr(index, 10);
                    spinner.css('visibility', 'visible');
                    $.ajax({
                        method: "GET",
                        url: "https://m.blrt.co/blrt/"+id+".json",
                        dataType: 'jsonp',
                        crossDomain: true,
                        success : function(response){
                            if(response.success){
                                data.push(response.data);
                                data[data.length-1].id = id;
                                var link  = '<p><a class="blrt-link" href = "https://e.blrt.com/embed/blrt/'+id + '">Blrt link</a>'
                                +'<a class="fallback-link" href="">Add fallback video</a>'
                                +'<span class="fallback-field"><input type="text" name="fallback_link"><button class="fallback-add">Add</button><button class="fallback-cancel">Cancel</button></span>'
                                +'</p>';
                                list.append('<li class="blrt-wp-url-single">'+'<h4 >'+response.data.title + '</h4><span class="dashicons dashicons-trash"></span><span class="dashicons dashicons-arrow-up-alt"></span><span class="dashicons dashicons-arrow-down-alt"></span>'+link+'</li>');
                                $(url_holder).val('');
                                message.text('Blrt added.');
                                
                            }
                            else{
                                alert('Please ensure you are using an individual Blrt link, not a Blrt Conversation link.');
                            }
                            spinner.css('visibility', 'hidden');
                        },
                        error: function(){
                            alert('Fail to query data');
                            spinner.css('visibility', 'hidden');
                        }
                    })
                    
                }
            }
        }
    }
    
    plugin.on('click','.dashicons-trash', function (e) {
        if(!confirm('Are you sure you want to remove this Blrt from the gallery?')){
            e.preventDefault();
            return false;
        }else{
            var li = $(e.target).closest('li');
            li.remove();
        }
        
    })

    plugin.on('click', '.trash', function(e){
        if(!confirm('Are you sure you want to delete this Blrt gallery?')){
            e.preventDefault();
            return false;
        }
        return true;
    });
    
    plugin.on('click', '.fallback-link', function(e) {
        e.preventDefault();
        $(e.originalEvent.target).parent().find('.fallback-field').css('opacity',1);
    })
    
    plugin.on('click', '.fallback-add', function(e) {
        e.preventDefault();
        var link = $(e.originalEvent.target).parent().find('input[name="fallback_link"]').val();
        var parent = $(e.originalEvent.target).closest('.blrt-wp-url-single');
        if(link != ''){
            parent.find('.fallback-link').attr('href',link).text('Fallback video');
        }
        else{
            parent.find('.fallback-link').attr('href',link).text('Add fallback video');
        }
        parent.find('.fallback-field').css('opacity',0);
    })
    
    plugin.on('click', '.fallback-cancel', function(e) {
        e.preventDefault();
        var parent = $(e.originalEvent.target).closest('.blrt-wp-url-single');
        parent.find('.fallback-field').css('opacity',0);
    })
    
     //move element down one step
    plugin.on('click', '.dashicons-arrow-down-alt', function(e) {
        var $el = $(e.originalEvent.target).closest('.blrt-wp-url-single');
        if ($el.not(':last-child'))
            $el.next().after($el);
    })
    
    //move element up one step
    plugin.on('click', '.dashicons-arrow-up-alt', function(e) {
        var $el = $(e.originalEvent.target).closest('.blrt-wp-url-single');
        if ($el.not(':first-child'))
            $el.prev().before($el);
    })

    
});