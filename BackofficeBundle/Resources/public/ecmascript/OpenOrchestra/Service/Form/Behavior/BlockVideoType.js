import AbstractBehavior from './AbstractBehavior'

/**
 * @class BlockVideoType
 */
class BlockVideoType extends AbstractBehavior
{
    /**
     * get extra events
     *
     * @return {Object}
     *
     * */
    getExtraEvents() {
        return {
            'change select#video_videoType': '_changeVideoType'
        }
    }

    /**
     * activate behavior
     * 
     * @param {Object} $element - jQuery object
     */
    activate($element) {
        let type = $('select#video_videoType', $element).val();
        $('[data-video-type]', $element).closest('.form-group').hide();
        $('[data-video-type="'+ type +'"]', $element).closest('.form-group').show();
    }

    /**
     * Toggle block when update video type input
     *
     * @param event
     * @private
     */
    _changeVideoType(event) {
        let type = $(event.currentTarget).val();
        $('[data-video-type]', this.$el).closest('.form-group').hide();
        $('[data-video-type="'+ type +'"]', this.$el).closest('.form-group').show();
    }

    /**
     * return selector
     * 
     * @return {String}
     */
    getSelector() {
        return 'form[name="video"]';
    }
}

export default (new BlockVideoType);
