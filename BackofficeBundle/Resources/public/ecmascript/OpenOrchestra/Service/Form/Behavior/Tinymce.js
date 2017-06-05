import AbstractBehavior from './AbstractBehavior'
import TinymceManager   from '../../Tinymce/TinymceManager'

/**
 * @class Tinymce
 */
class Tinymce extends AbstractBehavior
{
    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     * @param {AbstractFormView} view
     */
    activate($element, view) {
        $(view.$el).initialize('textarea#' + $element.attr('id'), (index, textarea) => {
            let editor = TinymceManager.createEditor($element);
            if ('disabled' === $(textarea).attr('disabled')) {
                editor.setMode('readonly');
            }
            view.getForm().bind('form:pre_submit', () => {
                editor.fire('submit', editor);
            });
        });
    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        TinymceManager.removeEditor($element);
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'textarea.tinymce';
    }
}

export default (new Tinymce);
