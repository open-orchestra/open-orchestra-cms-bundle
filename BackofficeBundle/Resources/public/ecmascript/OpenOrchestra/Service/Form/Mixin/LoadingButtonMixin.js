import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import FlashMessage     from '../../../Service/FlashMessage/FlashMessage'
import ApplicationError from '../../../Service/Error/ApplicationError'
import ConfirmModalView from '../../ConfirmModal/View/ConfirmModalView'
import Application      from '../../../Application/Application'

let LoadingButtonMixin = (superclass) => class extends superclass {

    activateLoading($button) {
        $button.addClass('active-loading');

        let $spinner = $('</i>');
        $spinner.addClass('fa fa-circle-o-notch fa-spin');

        $button.append($spinner);
    }
};

export default LoadingButtonMixin;
