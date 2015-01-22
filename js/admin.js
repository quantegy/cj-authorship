var cjAuthorship = {
    add: function(postId, authorName, authorDesc) {
        return jQuery.post(ajaxurl, {
            action:'cj_authorship_add',
            post_id:postId,
            author_name:authorName,
            desc:authorDesc
        }, null, 'json');
    },
    getAllByPost: function(postId) {
        return jQuery.get(ajaxurl, {
            action:'cj_authorship_get_all_by_post',
            post_id:postId
        } , null, 'html');
    },
    reorder: function(postId, authorId, ordinal) {
        return jQuery.post(ajaxurl, {
            action:'cj_authorship_reorder',
            post_id:postId,
            author_id:authorId,
            order:ordinal
        }, null, 'json');
    },
    update: function(postId, authorId, authorName, authorDesc) {
        return jQuery.post(ajaxurl, {
            action:'cj_authorship_update',
            post_id:postId,
            author_id:authorId,
            author_name:authorName,
            desc:authorDesc
        }, null, 'json');
    },
    delete: function(postId, authorId) {
        return jQuery.post(ajaxurl, {
            action:'cj_authorship_delete',
            post_id: postId,
            author_id:authorId
        }, null, 'json');
    },
    display: function(postId, isDisplayed) {
        return jQuery.post(ajaxurl, {
            action:'cj_authorship_display',
            post_id:postId,
            is_displayed:isDisplayed
        }, null, 'json');
    },
    init: function() {
        jQuery('#authorList').sortable({
            stop:function(e, ui) {
                jQuery('.authorListItem').each(function(i,v) {
                    var post_id = jQuery('#post_ID').val();
                    var author_id = jQuery(v).data('id');
                    var ordinal = i;
                    
                    jQuery.when(cjAuthorship.reorder(post_id, author_id, ordinal)).done(function(a) {});
                });
            }
        });
    }
};

jQuery(function($) {
    $(document).on('click', '.deleteAuthor', function(e) {
        var post_id = $('#post_ID').val();
        var author_id = $(this).data('id');
        
        $.when(cjAuthorship.delete(post_id, author_id)).done(function(a) {
            $.when(cjAuthorship.getAllByPost(post_id)).done(function(b) {
                $('#authorList').html(b);
                
                cjAuthorship.init();
            });
        });
    });
    
    $(document).on('click', '.updateAuthor', function(e) {
        var post_id = $('#post_ID').val();
        var author_id = $(this).data('id');
        var author_name = $(this).siblings('.listAuthorName').val();
        var author_desc = $(this).siblings('.listAuthorDesc').val();
        
        if(author_name == '') {
            $('<div />').attr({'class': 'errorFullname'}).css({
                'background-color': '#edb3dd',
                'border': '1px solid #990000',
                'color': '#990000',
                'padding': '5px',
                'margin': '10px 0 10px 0'
            }).html('Author name is required.').appendTo($(this).parent());
            
            return;
        } else {
            $(this).siblings('.errorFullname').remove();
        }
        
        $.when(cjAuthorship.update(post_id, author_id, author_name, author_desc)).done(function(a) {
            $.when(cjAuthorship.getAllByPost(post_id)).done(function(b) {
                $('#authorList').html(b);
                
                cjAuthorship.init();
            });
        });
    });
    
    $(document).on('click', '#authorAdd', function(e) {
        var post_id = $(this).data('post_id');
        var author_name = $('#fullname').val();
        var author_desc = $('#authorDesc').val();
        
        if(author_name == '') {
            $('<div />').attr({'id': 'errorFullname'}).css({
                'background-color': '#edb3dd',
                'border': '1px solid #990000',
                'color': '#990000',
                'padding': '5px',
                'margin': '10px 0 10px 0'
            }).html('Author name is required.').appendTo($('#fullname').parent());
            
            return;
        } else {
            $('#errorFullname').remove();
        }
        
        $.when(cjAuthorship.add(post_id, author_name, author_desc)).done(function(a) {
            $.when(cjAuthorship.getAllByPost(post_id)).done(function(b) {
                $('#authorList').html(b);
                
                $('#fullname').val('');
                $('#authorDesc').val('');
                
                cjAuthorship.init();
            });
        });
    });
    
    $(document).on('click', '#useCJAuthor', function(e) {
        console.log($(this).is(':checked'));
        var post_id = $('#post_ID').val();
        if($(this).is(':checked') === true) {
            $.when(cjAuthorship.display(post_id, 'yes')).done(function(a) {});
        } else {
            $.when(cjAuthorship.display(post_id, 'no')).done(function(a) {});
        }
    });
    
    cjAuthorship.init();
});
