import AbstractBehavior from './AbstractBehavior'

/**
 * @class InputFile
 */
class InputFile extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     */
    getExtraEvents() {
        return {
            'change input[type="file"]': '_showFileInfo'
        }
    }

    /**
     * @param event
     *
     * @private
     */
    _showFileInfo(event) {
        let $inputFile = event.currentTarget;
        let $fileInfo = $('.upload-file-info', $inputFile.closest('.oo-input-file'));
        let fileName = '';

        if ($inputFile.files && $inputFile.files.length == 1) {
            fileName = $inputFile.files[0].name;
        } else if ($inputFile.files && $inputFile.files.length > 1) {
            fileName = Translator.trans('open_orchestra_backoffice.form.file.multiple', {length: $inputFile.files.length});
        }

        $fileInfo.html(fileName);
    }

    /**
     * return selector
     *
     * @return {String}
     */
    getSelector() {
        return '.oo-input-file';
    }
}

export default (new InputFile);
