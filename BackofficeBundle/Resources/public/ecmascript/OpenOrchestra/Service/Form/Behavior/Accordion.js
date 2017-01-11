import AbstractBehavior   from './AbstractBehavior'
import Application        from '../../../Application/Application'
import SitesAvailable     from '../../../Application/Collection/Site/SitesAvailable'
import GroupListModalView from '../../../Application/View/Group/GroupListModalView'

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
            'click .remove-form': '_removeForm'
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
    }

    /**
     * add a form
     *
     * {Object} event
     */
    _addForm(event) {
        event.preventDefault();
        let $accordion = $(event.target).closest('.accordion')
        let $table = $accordion.find('table').eq(0);
        let prototype = $accordion.data('prototype');
        let prototypeName = /data-prototype-id=&quot;(.*?)&quot;/.exec(prototype)[1];
        let rank = -1;
        $('tbody[data-prototype-id]', $table).each(function(){
            rank = Math.max(parseInt($(this).data('prototypeId')) || 0, rank);
        })
        rank++;
        let regularExpression = new RegExp(prototypeName, 'g');
        let $prototype = $(_.unescape(prototype.replace(regularExpression, rank)));
        $table.append($prototype);
        $('body, html').animate({ scrollTop: $prototype.offset().top }, 1000);
    }

    /**
     * remove a form
     *
     * {Object} event
     */
    _removeForm(event) {
        event.preventDefault();
        $(event.target).closest('tbody').remove();
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
