import BBcodeTransformerManager from '../OrchestraBBCode/BBcodeTransformerManager'
import Application              from '../../../../Application/Application'
import InternalLinkModalView    from './View/InternalLinkModalView'

/**
 * @class InternalLinkPlugin
 */
class InternalLinkPlugin
{
    /**
     * Init plugin
     * @param {Editor} editor
     */
    init(editor) {
        editor.addButton('internal_link', {
            icon: 'internallink',
            tooltip: 'Insert/edit internal link',
            stateSelector: 'a[href][data-options]',
            onclick: () => {
                let selection = editor.selection.getNode();
                let data = {};
                if (typeof $(selection).attr('data-options') !== 'undefined') {
                    let options = $(selection).attr('data-options');
                    options = JSON.parse(options);
                    $.each(options, (name, value) => {
                        let splitName = name.split('_');
                        name = splitName.join('[');
                        if (splitName.length > 1) {
                            name = name.concat(']');
                        }
                        data[name] = value;
                    });
                }

                let internalLinkModalView = new InternalLinkModalView({editor: editor, data: data});
                Application.getRegion('modal').html(internalLinkModalView.render().$el);
                internalLinkModalView.show();
            }
        });

        // Add bbcode transformer if orchestra bbcode plugin is activate
        BBcodeTransformerManager.addHtmlToBbcodeTransformer('<a.*?data-options="([^"]*)".*?>(.*?)<\/a>', '[link=$1]$2[/link]')
    }

    /**
     * Information plugin
     */
    getInfo() {
        return {
            longname: 'Orchestra Internal link Plugin',
            author: 'open orchestra',
            infourl: 'www.open-orchestra.com'
        };
    }

}

tinymce.PluginManager.add('orchestra_internal_link', InternalLinkPlugin);
