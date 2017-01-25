import BBcodeTransformerManager from './BBcodeTransformerManager'

/**
 * BBcode plugin to convert html to bbcode when
 * event submit is fired
 * Submit is a custom event (@see tinymceBehavior)
 *
 * @class OrchestraBBcodePlugin
 */
class OrchestraBBcodePlugin
{
    /**
     * Init plugin
     * @param {Editor} editor
     */
    init(editor) {
        editor.on('submit', (e) => {
            editor.setContent(this.html2bbcode(e.getContent()));
        });
    }

    /**
     * Information plugin
     */
    getInfo(){
        return {
            longname: 'Orchestra BBCode Plugin',
            author: 'open orchestra',
            infourl: 'www.open-orchestra.com'
        };
    }

    /**
     * Convert Html to BBcode
     * @param {String} string
     */
    html2bbcode(string){
        let transformerList = BBcodeTransformerManager.getHtmlToBbcodeTransformer();
        string = tinymce.trim(string);
        $.each(transformerList, (regex, stringReplace) => {
            string = string.replace(new RegExp(regex,'gi'), stringReplace)
        });

        return string;
    }
}

tinymce.PluginManager.add('orchestra_bbcode',OrchestraBBcodePlugin);
