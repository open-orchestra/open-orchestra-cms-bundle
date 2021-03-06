import OrchestraView          from 'OpenOrchestra/Application/View/OrchestraView'
import Application            from 'OpenOrchestra/Application/Application'
import ServerError            from 'OpenOrchestra/Service/Error/ServerError'
import ApplicationError       from 'OpenOrchestra/Service/Error/ApplicationError'
import AreaView               from 'OpenOrchestra/Application/View/Area/AreaView'
import FlashMessageBag        from 'OpenOrchestra/Service/FlashMessage/FlashMessageBag'
import Node                   from 'OpenOrchestra/Application/Model/Node/Node'
import RenderToolbarViewMixin from 'OpenOrchestra/Application/View/Node/Mixin/RenderToolbarViewMixin'

/**
 * @class NodeView
 */
class NodeView extends mix(OrchestraView).with(RenderToolbarViewMixin)
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        this.events = {
            'click .area:not(.disabled)': '_activeArea'
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
        this._renderNodeActionToolbar($('.node-action-toolbar', this.$el), 'showNode');
        this._renderNodeTemplate($('.node-template .well', this.$el));

        return this;
    }

    /**
     * @param {Object} $selector
     * @private
     */
    _renderNodeTemplate($selector) {
        let templateNodeUrl =  this._getTemplatePath();
        $.get(templateNodeUrl)
            .done((data) => {
                data = data.replace(/\{\{(.*)\}\}/g, function(match, item){
                    return Translator.trans(item.trim());
                });
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
        if (this._node.get('rights').can_edit_data) {
            $(event.currentTarget).addClass('active');
        } else {
            $(event.currentTarget).addClass('read');
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
