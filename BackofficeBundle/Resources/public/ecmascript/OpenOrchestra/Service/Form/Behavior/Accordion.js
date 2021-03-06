import AbstractBehavior   from 'OpenOrchestra/Service/Form/Behavior/AbstractBehavior'
import Application        from 'OpenOrchestra/Application/Application'
import ConfirmModalView   from 'OpenOrchestra/Service/ConfirmModal/View/ConfirmModalView'

/**
 * @class Accordion
 */
class Accordion extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'click .open-form': '_toggleForm',
            'click .close-form': '_toggleForm',
            'click .open-forms': '_openForms',
            'click .close-forms': '_closeForms',
            'click .add-form': '_addForm',
            'click .remove-form': '_confirmRemove'
        }
    }

    /**
     * toggle form
     *
     * {Object} event
     */
    _toggleForm(event) {
        let $td = $(event.target).parent();
        $td.children().toggleClass('hide');
        $td.parent().next().toggleClass('hide');
        $td.parent().parent('.accordion-formrow-container').toggleClass('active');
    }

    /**
     * open forms
     *
     * {Object} event
     */
    _openForms(event) {
        event.preventDefault();
        let $table = $(event.target).closest('.accordion').find('table').eq(0);
        $('.open-form', $table).addClass('hide');
        $('.close-form', $table).removeClass('hide');
        $('tbody > tr:nth-of-type(2n)', $table).removeClass('hide');
        $table.find('.accordion-formrow-container').addClass('active');
    }

    /**
     * close forms
     *
     * {Object} event
     */
    _closeForms(event) {
        event.preventDefault();
        let $table = $(event.target).closest('.accordion').find('table').eq(0);
        $('.open-form', $table).removeClass('hide');
        $('.close-form', $table).addClass('hide');
        $('tbody > tr:nth-of-type(2n)', $table).addClass('hide');
        $table.find('.accordion-formrow-container').removeClass('active');
    }

    /**
     * add a form
     *
     * {Object} event
     */
    _addForm(event) {
        event.preventDefault();
        let $accordion = $(event.target).closest('.accordion');
        let $table = $accordion.find('table').eq(0);
        let prototype = $accordion.data('prototype');
        let prototypeName = /data-prototype-id=&quot;(.*?)&quot;/.exec(prototype)[1];
        let rank = -1;
        $('tbody[data-prototype-id]', $table).each(function(){
            rank = Math.max(parseInt($(this).data('prototypeId')) || 0, rank);
        });
        rank++;
        let regularExpression = new RegExp(prototypeName, 'g');
        let $prototype = $(_.unescape(prototype.replace(regularExpression, rank)));
        $table.append($prototype);
        $('thead', $table).removeClass('hide');
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.Events.trigger('form:activate', this);
        $('body, html').animate({ scrollTop: $prototype.offset().top }, 1000);
    }

    /**
     * Show modal confirm to remove
     *
     * @param {Object} event
     * @param {Object} context
     *
     * @returns {boolean}
     * @private
     */
    _confirmRemove(event, context) {
        event.stopPropagation();
        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove_prototype.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove_prototype.message'),
            yesCallback: $.proxy(context._removeForm, this),
            context: context,
            callbackParameter: [event]
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * remove a form
     *
     * {Object} event
     */
    _removeForm(event) {
        if (typeof this._removeForm !== 'undefined') {
            this._removeForm(event);
        } else {
            let $table = $(event.target).closest('table');
            $(event.target).closest('tbody').remove();
            if ($table.children('tbody').length === 0) {
                $('thead', $table).addClass('hide');
            }
        }
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.accordion';
    }
}

// unique instance of Accordion
export default (new Accordion);
