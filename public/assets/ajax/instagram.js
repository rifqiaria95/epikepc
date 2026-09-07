(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    function notify(message, type) {
        if (window.EpikSwal && typeof window.EpikSwal.notify === 'function') {
            return window.EpikSwal.notify(message, type || 'success');
        }
        if (window.toastr) {
            toastr[type || 'success'](message);
            return;
        }
        window.alert(message);
    }

    function confirmAction(message, options) {
        if (window.EpikSwal && typeof window.EpikSwal.confirm === 'function') {
            return window.EpikSwal.confirm(Object.assign({
                text: message,
                danger: true,
                confirmButtonText: 'Ya',
                cancelButtonText: 'Batal'
            }, options || {}));
        }
        return Promise.resolve(window.confirm(message));
    }

    var syncing = false;

    $('.btn-sync').on('click', function () {
        if (syncing) {
            return;
        }
        var btn = $(this);
        var scope = btn.data('scope');
        syncing = true;
        $('.btn-sync').prop('disabled', true);
        var original = btn.text();
        btn.text('Syncing…');

        $.ajax({
            url: window.instagramRoutes.sync,
            method: 'POST',
            data: { scope: scope }
        }).done(function (res) {
            notify(res.message || 'Sync completed.');
            window.setTimeout(function () { window.location.reload(); }, 600);
        }).fail(function (xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Sync failed. Please try again.';
            notify(message, 'error');
        }).always(function () {
            syncing = false;
            $('.btn-sync').prop('disabled', false);
            btn.text(original);
        });
    });

    $('#formInstagramSettings').on('submit', function (e) {
        e.preventDefault();
        var btn = $('#btn-save-settings').prop('disabled', true);
        $.ajax({
            url: window.instagramRoutes.settings,
            method: 'PUT',
            data: $(this).serialize()
        }).done(function (res) {
            notify(res.message || 'Settings saved.');
        }).fail(function (xhr) {
            notify((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to save settings.', 'error');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    $('.ig-visibility').on('change', function () {
        var input = $(this);
        $.ajax({
            url: window.instagramRoutes.toggle + '/' + input.data('id') + '/visibility',
            method: 'PATCH',
            data: { is_visible: input.is(':checked') ? 1 : 0 }
        }).done(function (res) {
            notify(res.message || 'Visibility updated.');
        }).fail(function () {
            input.prop('checked', !input.is(':checked'));
            notify('Unable to update visibility.', 'error');
        });
    });

    $('#formInstagramHighlight').on('submit', function (e) {
        e.preventDefault();
        var form = this;
        var btn = $('#btn-save-highlight').prop('disabled', true);
        var data = new FormData(form);

        $.ajax({
            url: window.instagramRoutes.highlights,
            method: 'POST',
            data: data,
            processData: false,
            contentType: false
        }).done(function (res) {
            notify(res.message || 'Highlight saved.');
            window.setTimeout(function () { window.location.reload(); }, 500);
        }).fail(function (xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to save highlight.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var first = Object.values(xhr.responseJSON.errors)[0];
                if (first && first[0]) {
                    message = first[0];
                }
            }
            notify(message, 'error');
        }).always(function () {
            btn.prop('disabled', false);
        });
    });

    $('.ig-highlight-visibility').on('change', function () {
        var input = $(this);
        $.ajax({
            url: window.instagramRoutes.highlightBase + '/' + input.data('id') + '/visibility',
            method: 'PATCH',
            data: { is_visible: input.is(':checked') ? 1 : 0 }
        }).done(function (res) {
            notify(res.message || 'Highlight visibility updated.');
        }).fail(function () {
            input.prop('checked', !input.is(':checked'));
            notify('Unable to update highlight visibility.', 'error');
        });
    });

    $('.ig-highlight-delete').on('click', function () {
        var id = $(this).data('id');
        confirmAction('Delete this highlight from the homepage?', {
            title: 'Delete highlight?',
            confirmButtonText: 'Delete'
        }).then(function (ok) {
            if (!ok) {
                return;
            }
            $.ajax({
                url: window.instagramRoutes.highlightBase + '/' + id,
                method: 'DELETE'
            }).done(function (res) {
                notify(res.message || 'Highlight deleted.');
                window.setTimeout(function () { window.location.reload(); }, 400);
            }).fail(function () {
                notify('Unable to delete highlight.', 'error');
            });
        });
    });

    var posting = false;
    $('#formInstagramPost').on('submit', function (e) {
        e.preventDefault();
    });

    $('#btn-schedule-post, #btn-publish-now').on('click', function (e) {
        e.preventDefault();
        if (posting) {
            return;
        }
        var publishNow = $(this).data('publish-now') == 1;
        if (!publishNow && !$('#post_scheduled_at').val()) {
            notify('Choose a schedule time, or use Publish now.', 'error');
            return;
        }
        if (!$('#post_media')[0].files.length) {
            notify('Choose a media file.', 'error');
            return;
        }

        posting = true;
        var btn = $(this).prop('disabled', true);
        var data = new FormData($('#formInstagramPost')[0]);
        data.set('publish_now', publishNow ? '1' : '0');
        if (publishNow) {
            data.delete('scheduled_at');
        }

        $.ajax({
            url: window.instagramRoutes.posts,
            method: 'POST',
            data: data,
            processData: false,
            contentType: false
        }).done(function (res) {
            notify(res.message || 'Post saved.');
            window.setTimeout(function () { window.location.reload(); }, 600);
        }).fail(function (xhr) {
            var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to save post.';
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var first = Object.values(xhr.responseJSON.errors)[0];
                if (first && first[0]) {
                    message = first[0];
                }
            }
            notify(message, 'error');
        }).always(function () {
            posting = false;
            btn.prop('disabled', false);
        });
    });

    $('.ig-post-cancel').on('click', function () {
        var id = $(this).data('id');
        confirmAction('Cancel this scheduled post?', {
            title: 'Cancel post?',
            confirmButtonText: 'Cancel post'
        }).then(function (ok) {
            if (!ok) {
                return;
            }
            $.ajax({
                url: window.instagramRoutes.postBase + '/' + id + '/cancel',
                method: 'POST'
            }).done(function (res) {
                notify(res.message || 'Cancelled.');
                window.setTimeout(function () { window.location.reload(); }, 400);
            }).fail(function (xhr) {
                notify((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to cancel.', 'error');
            });
        });
    });

    $('.ig-post-publish').on('click', function () {
        var id = $(this).data('id');
        confirmAction('Publish this post to Instagram now?', {
            title: 'Publish now?',
            confirmButtonText: 'Publish'
        }).then(function (ok) {
            if (!ok) {
                return;
            }
            $.ajax({
                url: window.instagramRoutes.postBase + '/' + id + '/publish',
                method: 'POST'
            }).done(function (res) {
                notify(res.message || 'Publishing…');
                window.setTimeout(function () { window.location.reload(); }, 600);
            }).fail(function (xhr) {
                notify((xhr.responseJSON && xhr.responseJSON.message) || 'Unable to publish.', 'error');
            });
        });
    });
})(jQuery);
