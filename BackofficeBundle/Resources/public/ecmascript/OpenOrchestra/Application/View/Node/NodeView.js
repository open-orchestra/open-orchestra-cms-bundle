import OrchestraView    from '../OrchestraView'
import Application      from '../../Application'
import ServerError      from '../../../Service/Error/ServerError'
import ApplicationError from '../../../Service/Error/ApplicationError'
import AreaView         from '../Area/AreaView'
import FlashMessageBag  from '../../../Service/FlashMessage/FlashMessageBag'
import Statuses         from '../../Collection/Status/Statuses'
import Status           from '../../Model/Status/Status'
import Node             from '../../Model/Node/Node'
import NodeToolbarView  from './NodeToolbarView'
import Nodes            from '../../Collection/Node/Nodes'
import NodeVersionsView from './NodeVersionsView'

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
            'click .btn-new-version': '_showNewVersionForm'
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
        this._displayLoader($('.well', this.$el));
        this._renderNodeActionToolbar($('.node-action-toolbar', this.$el));
        this._renderNodeTemplate($('.node-template .well', this.$el));

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderNodeActionToolbar($selector) {
        this._displayLoader($selector);
        let statuses = new Statuses();
        let nodeVersions = new Nodes();
        $.when(
            statuses.fetch({
                apiContext: 'node',
                urlParameter: {
                    nodeId: this._node.get('node_id'),
                    siteId: this._node.get('site_id'),
                    language: this._node.get('language'),
                    version: this._node.get('version')
                }
            }),
            nodeVersions.fetch({
                apiContext: 'list-version',
                urlParameter: {
                    nodeId: this._node.get('node_id'),
                    language: this._node.get('language')
                }
            })
        ).done( () => {
            let nodeToolbarView = new NodeToolbarView(
                {
                    node: this._node,
                    statuses: statuses,
                    nodeVersions: nodeVersions,
                    nodeView: this
                }
            );
            nodeToolbarView.listenTo(this, 'show.new_version.form', nodeToolbarView.newVersionForm);
            $selector.html(nodeToolbarView.render().$el);
        });
    }

    /**
     * Manage Version
     * @param {Nodes} nodeVersions
     */
    manageVersion(nodeVersions) {
        let nodeVersionsView = new NodeVersionsView({
            node: this._node,
            collection: nodeVersions
        });
        $('.well', this.$el).html(nodeVersionsView.render().$el);
    }

    /**
     * Show new version form
     *
     * @private
     */
    _showNewVersionForm() {
        this.trigger('show.new_version.form');
        $('.alert-published-state').hide();
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
                if (false === this._node.get('status').get('published_state')) {
                    this._activateSortableBlocks($template);
                }
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
        let areaView = new AreaView({area: area,  node: this._node});
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
     * Activate sortable blocks
     *
     * @param {Object} $template
     *
     * @private
     */
    _activateSortableBlocks($template) {
        $('.block-container > div', $template).sortable({
            items: '> .block-item',
            handle: '.move-block',
            connectWith: '.block-container > div',
            update: (event, ui) => {
                let $blockItem = $(ui.item);
                let $area = ui.sender || $blockItem.parent();
                let area = $area.data('area');
                area.set('hasChanged', true);
                let $blocks = $area.children('.block-item');
                let blocks = _.map($blocks, ($block) => {
                    return $($block).data('block')
                });

                area.get('blocks').set(blocks);
            },
            stop: () => {
                let changedAreas = this._node.getChangedArea();
                let areas = this._node.get('areas');
                this._node.save({'areas': changedAreas}, {
                    apiContext: 'update_position_block',
                    urlParameter: {
                        'nodeId': this._node.get('node_id'),
                        'siteId': this._node.get('site_id'),
                        'language': this._node.get('language'),
                        'version': this._node.get('version')
                    }
                });
                for (let index in changedAreas) {
                    changedAreas[index].set('hasChanged', false);
                }
                this._node.set('areas', areas);
            }
        });
    }
}

export default NodeView;
