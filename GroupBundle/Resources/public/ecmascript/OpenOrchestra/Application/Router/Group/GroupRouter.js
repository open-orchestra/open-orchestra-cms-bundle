import OrchestraRouter from 'OpenOrchestra/Application/Router/OrchestraRouter'
import Application     from 'OpenOrchestra/Application/Application'
import FormBuilder     from 'OpenOrchestra/Service/Form/Model/FormBuilder'
import Groups          from 'OpenOrchestra/Application/Collection/Group/Groups'
import GroupsView      from 'OpenOrchestra/Application/View/Group/GroupsView'
import GroupFormView   from 'OpenOrchestra/Application/View/Group/GroupFormView'
import SitesAvailable  from 'OpenOrchestra/Application/Collection/Site/SitesAvailable'

/**
 * @class GroupRouter
 */
class GroupRouter extends OrchestraRouter
{
    /**
     * @inheritdoc
     */
    preinitialize(options) {
        this.routes = {
            'group/list(/:page)' : 'listGroup',
            'group/edit/:groupId': 'editGroup',
            'group/new'          : 'newGroup'
        };
    }

    /**
     * @inheritdoc
     */
    getBreadcrumb() {
        return [
            {
                label:Translator.trans('open_orchestra_user_admin.menu.user.title')
            },
            {
                label: Translator.trans('open_orchestra_group.menu.group.group'),
                link: '#'+Backbone.history.generateUrl('listGroup')
            }
        ]
    }

    /**
     * @inheritdoc
     */
    getMenuHighlight() {
        return {
            '*' : 'navigation-group'
        };
    }

    /**
     *  List Group
     *
     * @param {String} page
     */
    listGroup(page) {
        if (null === page) {
            page = 1
        }
        this._displayLoader(Application.getRegion('content'));
        let pageLength = Application.getConfiguration().getParameter('datatable').pageLength;
        page = Number(page) - 1;
        new SitesAvailable().fetch({
            success: (sites) => {
                new Groups().fetch({
                    data : {
                        start: page * pageLength,
                        length: pageLength
                    },
                    success: (groups) => {
                        let groupsView = new GroupsView({
                            collection: groups,
                            settings: {
                                page: page,
                                deferLoading: [groups.recordsTotal, groups.recordsFiltered],
                                data: groups.models,
                                pageLength: pageLength
                            },
                            sites: sites
                        });
                        let el = groupsView.render().$el;
                        Application.getRegion('content').html(el);
                    }
                });
            }
        });
    }

    /**
     * Edit Group
     *
     * @param  {String} groupId
     */
    editGroup(groupId) {
        let url = Routing.generate('open_orchestra_group_form', {groupId: groupId});
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let groupFormView = new GroupFormView({
                form: form,
                groupId: groupId
            });
            Application.getRegion('content').html(groupFormView.render().$el);
        });
    }
    
    /**
     * Create Group
     */
    newGroup() {
        let url = Routing.generate('open_orchestra_group_new');
        this._displayLoader(Application.getRegion('content'));
        FormBuilder.createFormFromUrl(url, (form) => {
            let groupFormView = new GroupFormView({
                form: form
             });
            Application.getRegion('content').html(groupFormView.render().$el);
        });
    }
    
}

export default GroupRouter;
