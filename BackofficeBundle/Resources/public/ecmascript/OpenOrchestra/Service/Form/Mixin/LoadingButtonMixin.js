let LoadingButtonMixin = (superclass) => class extends superclass {

    /**
     * @param {Object} $button - selector jquery
     */
    activateLoading($button) {
        let $srOnly = $('<span>');
        $srOnly.addClass('invisible');
        $srOnly.html($button.html());

        let $spinner = $('<i>');
        $spinner.addClass('fa fa-circle-o-notch fa-spin');

        $button.html($srOnly);
        $button.append($spinner);
        $button.addClass('active-loading disabled');
        $button.prop('disabled', true);
    }

    /**
     * @param {Object} $button - selector jquery
     */
    resetLoadingButton($button) {
        if ($button.hasClass('active-loading')) {
            $button.removeClass('active-loading disabled');
            $button.prop('disabled', false);

            let contentButton = $('span.invisible', $button).html();
            $button.html(contentButton);
        }
    }
};

export default LoadingButtonMixin;
