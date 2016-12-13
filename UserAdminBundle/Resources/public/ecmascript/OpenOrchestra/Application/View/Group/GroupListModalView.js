import OrchestraView        from '../OrchestraView'
import ModalView            from '../../../Service/Modal/View/ModalView'
import UserGroups               from '../../Collection/Group/UserGroups'
import GroupListForUserView from './GroupListForUserView'
import Application          from '../../Application'

/**
 * @class GroupListModalView
 */
class GroupListModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        $.extend(this.events, {
            'click .select-group': '_selectGroup',
            'click .search-engine button.submit, .search-engine button.reset': '_search',
        });
    }

    /**
     * Initialize
     * @param {Sites} sites
     * @param {array} blockedGroups
     * @param {array} selectedGroups
     */
    initialize({sites, blockedGroups, selectedGroups}) {
        this._sites = sites;
        this._blockedGroups = blockedGroups;
        this._selectedGroups = selectedGroups;
    }

    /**
     * Render Site selector
     */
    render() {
        this._collection = new UserGroups();
        this._groupListForUserView = new GroupListForUserView({
            collection: this._collection,
            blockedGroups: this._blockedGroups,
            selectedGroups: this._selectedGroups
        });

        let template = this._renderTemplate('User/groupListModalView',
            {
                sites: this._sites.models,
                language: Application.getContext().language
            }
        );
        this.$el.html(template);
        $('.modal-body .groups-list', this.$el).html(this._groupListForUserView.render().$el);

        return this;
    }

    /**
     * Search node in list
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();
        let filters = {};
        if ($(event.target).hasClass('submit')) {
            $('button.reset', this.$el).removeClass('hidden');
        }
        if ($(event.target).hasClass('reset')) {
            $('button.reset', this.$el).addClass('hidden');
            $('form.search-engine', this.$el).trigger('reset');
        }

        let formData = $('form.search-engine', this.$el).serializeArray();
        for (let data of formData) {
            filters[data.name] = data.value;
        }

        this._groupListForUserView.filter(filters);

        return false;
    }

    /**
     * Select groups
     */
    _selectGroup() {
        let formGroups = _.pluck($('[name="group"]', this.$el).removeAttr('disabled').serializeArray(), 'value');
        let selectedGroups = [];
        for (let group of this._collection.models) {
            if (formGroups.indexOf(group.get('id')) > -1) {
                selectedGroups.push(group);
            }
        }
        Backbone.Events.trigger('group:select', selectedGroups);
        this.$el.modal('hide');
    }
}

export default GroupListModalView;
