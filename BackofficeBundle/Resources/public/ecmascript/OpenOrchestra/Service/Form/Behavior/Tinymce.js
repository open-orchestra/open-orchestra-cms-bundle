import AbstractBehavior from './AbstractBehavior'

/**
 * @class Tinymce
 */
class Tinymce extends AbstractBehavior
{
    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        console.log($element);
        let id = $element.attr('id');
        console.log(id);
        let settings = {
            selector: id
        };
        console.log(settings);
        console.log($element);
        console.log(this);
        console.log($('textarea.tinymce'));
        tinymce.baseUrl = '/tinymce';
        let $div = $('<div/>');
        var el = document.createElement('textarea');
        tinymce.init({target: el});
        /*console.log($div);
        console.log($($element, $div));
        console.log($div);*/

    }

    /**
     * deactivate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    deactivate($element) {
        //$element.tooltip('destroy');
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
