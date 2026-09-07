/**
 * EPIK SweetAlert2 helpers — matches Vuexy/Sneat template styling.
 * Depends on global Swal from assets/vendor/libs/sweetalert2/sweetalert2.js
 */
(function (window) {
    'use strict';

    var buttonClasses = {
        confirmButton: 'btn btn-primary waves-effect waves-light',
        cancelButton: 'btn btn-label-secondary waves-effect waves-light',
        denyButton: 'btn btn-label-danger waves-effect waves-light'
    };

    function hasSwal() {
        return typeof window.Swal !== 'undefined' && typeof window.Swal.fire === 'function';
    }

    function base(options) {
        return Object.assign({
            buttonsStyling: false,
            customClass: buttonClasses,
            reverseButtons: true
        }, options || {});
    }

    function toast(icon, title, text) {
        if (!hasSwal()) {
            window.console && console.warn(title || text);
            return Promise.resolve();
        }

        return window.Swal.fire(base({
            icon: icon || 'info',
            title: title || '',
            text: text || '',
            confirmButtonText: 'OK'
        }));
    }

    function success(message, title) {
        return toast('success', title || 'Success', message);
    }

    function error(message, title) {
        return toast('error', title || 'Error', message);
    }

    function warning(message, title) {
        return toast('warning', title || 'Warning', message);
    }

    function info(message, title) {
        return toast('info', title || 'Info', message);
    }

    /**
     * @param {string|object} messageOrOptions
     * @param {string} [title]
     * @returns {Promise<boolean>}
     */
    function confirm(messageOrOptions, title) {
        var options = typeof messageOrOptions === 'object' && messageOrOptions !== null
            ? messageOrOptions
            : {
                title: title || 'Are you sure?',
                text: String(messageOrOptions || ''),
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            };

        if (!hasSwal()) {
            return Promise.resolve(window.confirm(options.text || options.title || 'Continue?'));
        }

        return window.Swal.fire(base({
            icon: options.icon || 'warning',
            title: options.title || 'Are you sure?',
            text: options.text || '',
            showCancelButton: true,
            focusCancel: true,
            confirmButtonText: options.confirmButtonText || 'Yes',
            cancelButtonText: options.cancelButtonText || 'Cancel',
            customClass: {
                confirmButton: options.confirmButtonClass || (options.danger
                    ? 'btn btn-danger waves-effect waves-light'
                    : buttonClasses.confirmButton),
                cancelButton: buttonClasses.cancelButton
            }
        })).then(function (result) {
            return !!(result && result.isConfirmed);
        });
    }

    function notify(message, type) {
        var icon = type === 'error' || type === 'danger'
            ? 'error'
            : (type === 'warning' ? 'warning' : (type === 'info' ? 'info' : 'success'));

        if (window.toastr && typeof window.toastr[icon === 'error' ? 'error' : (icon === 'warning' ? 'warning' : (icon === 'info' ? 'info' : 'success'))] === 'function') {
            var toastFn = icon === 'error' ? 'error' : (icon === 'warning' ? 'warning' : (icon === 'info' ? 'info' : 'success'));
            window.toastr[toastFn](message);
            return Promise.resolve();
        }

        return toast(icon, icon === 'success' ? 'Success' : (icon === 'error' ? 'Error' : 'Notice'), message);
    }

    window.EpikSwal = {
        fire: function (options) {
            return hasSwal() ? window.Swal.fire(base(options)) : Promise.resolve();
        },
        success: success,
        error: error,
        warning: warning,
        info: info,
        confirm: confirm,
        notify: notify,
        toast: toast
    };

    // Soft-override native alert so leftover callers use the template.
    if (hasSwal()) {
        window.alert = function (message) {
            notify(String(message == null ? '' : message), 'info');
        };
    }
})(window);
