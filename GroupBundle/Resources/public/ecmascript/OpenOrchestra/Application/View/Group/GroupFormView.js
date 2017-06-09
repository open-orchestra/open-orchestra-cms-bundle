import AbstractFormView     from '../../../Service/Form/View/AbstractFormView'
import Application          from '../../Application'
import Group                from '../../Model/Group/Group'
import Users                from '../../Collection/User/Users'
import FormViewButtonsMixin from '../../../Service/Form/Mixin/FormViewButtonsMixin'
import MembersListView      from '../User/MembersListView'
import ApplicationError     from '../../../Service/Error/ApplicationError'

/**
 * @class GroupFormView
 */
class GroupFormView extends mix(AbstractFormView).with(FormViewButtonsMixin)
{

    /**
     * Initialize
     * @param {Form}   form
     * @param {String} groupId
     */
    initialize({form, groupId = null}) {
        super.initialize({form : form});
        this._groupId = groupId;
    }

    /**
     * @inheritdoc
     */
    render() {
        let title = Translator.trans('open_orchestra_group.table.groups.new');
        if (null !== this._groupId) {
            title = $("input[id*='oo_group_labels_']", this._form.$form).first().val();
            let currentTitle = $("input[id='oo_group_labels_" + Application.getContext().get('language') + "']", this._form.$form);
            if (currentTitle.length > 0) {
                title = currentTitle.val();
            }
        }
        let template = this._renderTemplate('Group/groupFormView', {
            title: title
        });
        this.$el.html(template);
        this._$formRegion = $('.form-edit', this.$el);
        super.render();

        return this;
    }

    /**
     * @inheritdoc
     */
    _renderForm() {
        super._renderForm();
        if (null !== this._groupId) {
            this._addTabMemberList();
        }
    }


    /**
     * Add tab pane member list
     * @private
     */
    _addTabMemberList() {
        let tabId = 'tab-members-list';
        let $navTab = $('<li/>').append(
            $('<a/>', {
                text: Translator.trans('open_orchestra_group.group.tab_members_list'),
                'data-toggle': 'tab',
                role: 'tab',
                href: '#'+tabId
            })
        );
        let $tabContent = $('<div/>', {
            class: 'tab-pane',
            id: tabId,
            role: 'tabpanel'
        });

        $('form > .nav-tabs', this._$formRegion).append($navTab);
        $('form > .tab-content', this._$formRegion).append($tabContent);

        let listView = this._createMembersListView();
        $tabContent.html(listView.render().$el);
    }

    /**
     * @returns {MembersListView}
     *
     * @private
     */
    _createMembersListView() {
        let collection = new Users();
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;

        return new MembersListView({
            collection: collection,
            groupId: this._groupId,
            settings: {
                pageLength: pageLength
            }
        });
    }

    /**
     * Redirect to edit group view
     *
     * @param {mixed}  data
     * @param {string} textStatus
     * @param {object} jqXHR
     * @private
     */
    _redirectEditElement(data, textStatus, jqXHR) {
        let groupId = jqXHR.getResponseHeader('groupId');
        let url = Backbone.history.generateUrl('editGroup', {
            groupId: groupId
        });
        Backbone.Events.trigger('form:deactivate', this);
        Backbone.history.navigate(url, true);
    }

    /**
     * Delete
     */
    _deleteElement() {
        if (null === this._groupId) {
            throw new ApplicationError('Invalid groupId');
        }
        let group = new Group({'id': this._groupId});
        group.destroy({
            success: () => {
                let url = Backbone.history.generateUrl('listGroup');
                Backbone.history.navigate(url, true);
            }
        });
    }
}

export default GroupFormView;
