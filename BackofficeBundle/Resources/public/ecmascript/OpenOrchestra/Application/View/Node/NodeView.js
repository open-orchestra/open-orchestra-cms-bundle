import OrchestraView    from '../OrchestraView'
import Application      from '../../Application'
import ServerError      from '../../../Service/Error/ServerError'
import ApplicationError from '../../../Service/Error/ApplicationError'
import AreaView         from '../Area/AreaView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import Statuses         from '../../Collection/Status/Statuses'
import Status           from '../../Model/Status/Status'

/**
 * @class NodeView
 */
class NodeView extends OrchestraView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .area:not(.disabled)': '_activeArea',
            'click .dropdown-workflow li a': '_changeStatus'
        }
    }

    /**
     * Initialize
     * @param {Node}   node
     * @param {Array}  siteLanguages
     */
    initialize({node, siteLanguages}) {
        this._node = node;
        this._siteLanguages = siteLanguages;
    }

    /**
     * Render node
     */
    render() {
        let template = this._renderTemplate('Node/nodeView',
            {
                node: this._node,
                siteLanguages: this._siteLanguages,
                messages: FlashMessageBag.getMessages()
            }
        );
        this.$el.html(template);
        this._diplayLoader($('.well', this.$el));
        this._diplayLoader($('.node-action-toolbar', this.$el));
        this._renderNodeActionToolbar($('.node-action-toolbar', this.$el));
        this._renderNodeTemplate($('.node-template .well', this.$el));

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderNodeActionToolbar($selector) {
        let statuses = new Statuses();
        $.when(
            statuses.fetch({
                context: 'node',
                urlParameter: {
                    nodeId: this._node.get('node_id'),
                    siteId: this._node.get('site_id'),
                    language: this._node.get('language'),
                    version: this._node.get('version')
                }
            })
        ).done( () => {
            let template = this._renderTemplate('Node/nodeToolbarView',
                {
                    node: this._node,
                    statuses: statuses.models
                }
            );
            $selector.html(template);
        });
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderNodeTemplate($selector) {
        let templateNodeUrl =  this._getTemplatePath();
        $.get(templateNodeUrl)
            .done((data) => {
                let $template = $(data);
                this._renderAreas($template);
                $selector.html($template);
            })
            .fail((xhr, textStatus) => {
                let error = new ServerError(xhr.status, textStatus, 'Failed to load template');
                Backbone.Events.trigger('application:error', error);
            });
    }

    /**
     * @returns {String}
     * @private
     */
    _getTemplatePath() {
        let templateConfig = Application.getConfiguration().getParameter('templateSet');
        let templateSetName = this._node.get('template_set');
        let templateName = this._node.get('template');
        if (templateConfig.hasOwnProperty(templateSetName)) {
            let templateSet = templateConfig[templateSetName];
            if (templateSet.templates.hasOwnProperty(templateName)) {
                return templateSet.templates[templateName].path;
            }
        }

        throw new ApplicationError('Not found template '+ templateName + ' in template set ' + templateSetName);
    }

    /**
     * @param {Object} $template
     * @private
     */
    _renderAreas($template) {
        let $areas = $('.area:not(.disabled)', $template);
        $.each($areas, (index, areaContainer) => {
            this._renderArea(areaContainer);
        })
    }

    /**
     * @param {Object} areaContainer
     * @private
     */
    _renderArea(areaContainer) {
        let areaId = $(areaContainer).attr('data-area-id');
        if (null === areaId) {
            throw  new ApplicationError('Missing area id');
        }

        let $blockContainer = $('.block-container', $(areaContainer));
        if (0 === $blockContainer.length){
            throw  new ApplicationError('Missing block container in area: ' + areaId);
        }

        let area = this._node.getArea(areaId);
        let areaView = new AreaView({area: area});
        $blockContainer.html(areaView.render().$el);
    }

    /**
     * @param {Object} event
     * @private
     */
    _activeArea(event) {
        $('.area', this.$el).removeClass('active');
        if (this._node.get('rights').can_edit) {
            $(event.currentTarget).addClass('active');
        }
    }

    /**
     * @param {Object} event
     * @private
     */
    _changeStatus(event) {
        let statusId = $(event.currentTarget).attr('data-id');
        let status = new Status({id: statusId});
        this._node.save({ 'status': status}, {
            success: (data) => {
                Backbone.history.loadUrl(Backbone.history.fragment);
            }
        });
    }
}

export default NodeView;
