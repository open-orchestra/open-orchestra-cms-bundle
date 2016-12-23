import OrchestraView           from '../OrchestraView'
import WorkflowProfileListView from '../../View/WorkflowProfile/WorkflowProfileListView'
import Application             from '../../Application'

/**
 * @class WorkflowProfilesView
 */
class WorkflowProfilesView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .search-engine button.submit': '_search',
            'click .btn-delete': '_remove'
        }
    }

    /**
     * @inheritdoc
     */
    initialize({collection, settings}) {
        this._collection = collection;
        this._settings = settings;
    }

    /**
     * Render workflow profiles view
     */
    render() {
        if (0 === this._collection.recordsTotal) {
            let template = this._renderTemplate('List/emptyListView' , {
                title: Translator.trans('open_orchestra_workflow_admin.workflow_profile.title_list'),
                urlAdd: ''
            });
            this.$el.html(template);
        } else {
            let template = this._renderTemplate('WorkflowProfile/workflowProfileView',
            {
                language: Application.getContext().language
            });
            this.$el.html(template);
            this._listView = new WorkflowProfileListView({
                collection: this._collection,
                settings: this._settings
            });
            $('.workflow-profile-list', this.$el).html(this._listView.render().$el);
        }

        return this;
    }


    /**
     * Search workflow profile in list
     * @param {Object} event
     *
     * @returns {boolean}
     * @private
     */
    _search(event) {
        event.stopPropagation();

        let formData = $('form.search-engine', this.$el).serializeArray();
        let filters = {};
        for (let data of formData) {
            filters[data.name] = data.value;
        }
        this._listView.filter(filters);

        return false;
    }

    /**
     * Remove
     *
     * @private
     */
    _remove() {
        let workflowProfiles = this._collection.where({'delete': true});
        this._collection.destroyModels(workflowProfiles, {
            success: () => {
                this._listView.api.draw(false);
            }
        });
    }
}

export default WorkflowProfilesView;
