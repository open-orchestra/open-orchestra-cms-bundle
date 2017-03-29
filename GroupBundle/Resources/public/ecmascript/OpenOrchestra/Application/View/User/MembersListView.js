import AbstractDataTableView from '../../../Service/DataTable/View/AbstractDataTableView'
import Application           from '../../Application'
import ConfirmModalView      from '../../../Service/ConfirmModal/View/ConfirmModalView'
import User                  from '../../Model/User/User'

/**
 * @class MembersListView
 */
class MembersListView extends AbstractDataTableView
{
    /**
     * @param {Object} collection
     * @param {Array}  settings
     * @param {Array}  groupId
     */
    preinitialize({collection, settings, groupId}) {
        super.preinitialize({collection:collection, seottings:settings});
        this._groupId = groupId;
        this.events = {
            'click .delete-icon': '_confirmDelete'
        }
    }

    /**
     * @inheritDoc
     */
    getTableId() {
        return 'members_list';
    }

    /**
     * @inheritDoc
     */
    getColumnsDefinition() {
        return [
            {
                name: "username",
                title: Translator.trans('open_orchestra_group.table.members_list.username'),
                orderable: true,
                orderDirection: 'desc',
                visibile: true,
                createdCell: this._createEditLink
            },
            {
                name: "email",
                title: Translator.trans('open_orchestra_group.table.members_list.email'),
                orderable: true,
                visibile: true
            },
            {
                name: "delete",
                orderable: false,
                visibile: true,
                width: '20px',
                createdCell: this._createDeleteIcon,
                render: () => { return ''}
            }
        ];
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     * @private
     */
    _createEditLink(td, cellData, rowData) {
        let link = Backbone.history.generateUrl('editUser', {
            userId: rowData.get('id')
        });
        cellData = $('<a>',{
            text: cellData,
            href: '#'+link
        });

        $(td).html(cellData)
    }

    /**
     * @param {Object} td
     * @param {Object} cellData
     * @param {Object} rowData
     *
     * @private
     */
    _createDeleteIcon(td, cellData, rowData) {
        let $icon = $('<i>', {'aria-hidden': 'true', class:'delete-icon fa fa-trash'});
        $icon.data(rowData);
        $(td).append($icon);
    }

    /**
     * Show modal confirm to delete user of current group
     *
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _confirmDelete(event) {
        event.stopPropagation();
        let user = new User($(event.currentTarget).data().attributes);

        let confirmModalView = new ConfirmModalView({
            confirmTitle: Translator.trans('open_orchestra_backoffice.confirm_remove.title'),
            confirmMessage: Translator.trans('open_orchestra_backoffice.confirm_remove.message'),
            yesCallback: this._removeGroup,
            context: this,
            callbackParameter: [user]
        });

        Application.getRegion('modal').html(confirmModalView.render().$el);
        confirmModalView.show();

        return false;
    }

    /**
     * Remove group
     * @param {User} user
     * @private
     */
    _removeGroup(user) {
        if (null === this.api || typeof this.api === "undefined") {
            throw TypeError("Parameter api should be an instance of AbstractDataTableView");
        }
        user.save({}, {
            urlParameter: {
                groupId: this._groupId
            },
            success: () => {
                this.api.draw(false);
            }
        });
    }

    /**
     * @inheritDoc
     */
    _getSyncOptions() {
        return {
            apiContext: 'members_list',
            urlParameter: {
                groupId: this._groupId
            }
        };
    }
}

export default MembersListView;
