'use restrict';

$('document').ready(function () {
    $('.remove-cmt').on('click', function (e) {
        var _this = $(this);
        e.preventDefault();
        var data = {
            article_id: _this.parents('tr.comment_row').find('.article_id').html(),
            comment_id: _this.parents('tr.comment_row').find('.comment_id').html(),
            comment_text: _this.parents('tr.comment_row').find('.comment_text').html(),
            user_full_name: _this.parents('tr.comment_row').find('.user_full_name').html(),
            user_avatar: _this.parents('tr.comment_row').find('.user_avatar').data('user_avatar')
        };

        $.ajax({
            url: '/comment/deleteComment',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function () {
                _this.parents('tr.comment_row').remove()
            },
            error: function (error) {
                alert('Error: ' + error.responseText);
            }
        });
    });

    $('.remove-reply').on('click', function (e) {
        var _this = $(this);
        e.preventDefault();
        var data = {
            article_id:  _this.parents('tr.reply_row').find('.article_id').html(),
            comment_id: _this.parents('tr.reply_row').find('.parent_id').html(),
            reply_id: _this.parents('tr.reply_row').find('.reply_id').html(),
            reply_text: _this.parents('tr.reply_row').find('.reply_text').html(),
            user_avatar: _this.parents('tr.reply_row').find('.user_avatar').data('user_avatar'),
            user_full_name: _this.parents('tr.reply_row').find('.user_full_name').html()
        };

        console.log(data);

        $.ajax({
            url: '/comment/deleteReply',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function () {
                _this.parents('tr.reply_row').remove()
            },
            error: function (error) {
                alert('Error: ' + error.responseText);
            }
        });
    });
});

function deActivateCmt(obj) {
    var _this = obj;
    var data = {
        article_id:  _this.parents('tr.comment_row').find('.article_id').html(),
        comment_id: _this.parents('tr.comment_row').find('.comment_id').html(),
        comment_text: _this.parents('tr.comment_row').find('.comment_text').html(),
        user_name: _this.parents('tr.comment_row').find('.username').html(),
        user_full_name: _this.parents('tr.comment_row').find('.user_full_name').html(),
        user_id: _this.parents('tr.comment_row').find('.user_id').html(),
        user_avatar: _this.parents('tr.comment_row').find('.user_avatar').data('user_avatar')
    };


    $.ajax({
        type: 'post',
        url: '/comment/deActiveCmt',
        data: data,
        dataType: 'json',
        success: function () {
            _this.removeClass('btn-success').addClass('btn-danger');
            _this.find('span.ladda-label').html('Suspended');
            _this.attr('onclick', 'activateCmt($(this))');
        },
        error: function (error) {
            alert('Error: ' + error.responseText);
        }
    });
}

function activateCmt(obj) {
    var _this = obj;
    var data = {
        article_id:  _this.parents('tr.comment_row').find('.article_id').html(),
        comment_id: _this.parents('tr.comment_row').find('.comment_id').html(),
        comment_text: _this.parents('tr.comment_row').find('.comment_text').html(),
        user_name: _this.parents('tr.comment_row').find('.username').html(),
        user_full_name: _this.parents('tr.comment_row').find('.user_full_name').html(),
        user_id: _this.parents('tr.comment_row').find('.user_id').html(),
        user_avatar: _this.parents('tr.comment_row').find('.user_avatar').data('user_avatar')
    };

    $.ajax({
        type: 'post',
        url: '/comment/activeCmt',
        data: data,
        dataType: 'json',
        success: function () {
            _this.removeClass('btn-danger').addClass('btn-success');
            _this.find('span.ladda-label').html('Active');
            _this.attr('onclick', 'deActivateCmt($(this))');
        },
        error: function (error) {
            alert('Error: ' + error.responseText);
        }
    });

}

function deActivateRep(obj) {
    var _this = obj;
    var data = {
        article_id:  _this.parents('tr.reply_row').find('.article_id').html(),
        parent_id: _this.parents('tr.reply_row').find('.parent_id').html(),
        reply_id: _this.parents('tr.reply_row').find('.reply_id').html(),
        reply_text: _this.parents('tr.reply_row').find('.reply_text').html(),
        user_name: _this.parents('tr.reply_row').find('.username').html(),
        user_full_name: _this.parents('tr.reply_row').find('.user_full_name').html(),
        user_id: _this.parents('tr.reply_row').find('.user_id').html(),
        user_avatar: _this.parents('tr.reply_row').find('.user_avatar').data('user_avatar')
    };

    $.ajax({
        type: 'post',
        url: '/comment/deActiveRep',
        data: data,
        dataType: 'json',
        success: function () {
            _this.removeClass('btn-success').addClass('btn-danger');
            _this.find('span.ladda-label').html('Suspended');
            _this.attr('onclick', 'activateRep($(this))');
        },
        error: function (error) {
            alert('Error: ' + error.responseText);
        }
    });
}

function activateRep(obj) {
    var _this = obj;
    var data = {
        article_id:  _this.parents('tr.reply_row').find('.article_id').html(),
        parent_id: _this.parents('tr.reply_row').find('.parent_id').html(),
        reply_id: _this.parents('tr.reply_row').find('.reply_id').html(),
        reply_text: _this.parents('tr.reply_row').find('.reply_text').html(),
        user_name: _this.parents('tr.reply_row').find('.username').html(),
        user_full_name: _this.parents('tr.reply_row').find('.user_full_name').html(),
        user_id: _this.parents('tr.reply_row').find('.user_id').html(),
        user_avatar: _this.parents('tr.reply_row').find('.user_avatar').data('user_avatar')
    };

    $.ajax({
        type: 'post',
        url: '/comment/activeRep',
        data: data,
        dataType: 'json',
        success: function () {
            _this.removeClass('btn-danger').addClass('btn-success');
            _this.find('span.ladda-label').html('Active');
            _this.attr('onclick', 'deActivateRep($(this))');
        },
        error: function (error) {
            alert('Error: ' + error.responseText);
        }
    });

}

function load_more(obj) {
    var _this = obj;
    var data = {
        page: parseInt(_this.attr('value'))
    };

    $.ajax({

    });
}