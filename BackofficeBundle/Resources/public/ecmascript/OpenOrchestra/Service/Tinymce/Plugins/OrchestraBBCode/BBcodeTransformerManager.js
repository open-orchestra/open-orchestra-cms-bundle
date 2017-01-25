/**
 * @class BBcodeTransformerManager
 */
class BBcodeTransformerManager
{
    /**
     * Constructor
     */
    constructor() {
        this._htmlToBbcodeTransformer = {};
    }

    /**
     * @param {string} htmlExpression
     * @param {string} bbcodeExpression
     *
     * For instance addHtmlToBbcodeTransformer('<a.*?href=\"(.*?)\".*?>(.*?)<\/a>', '[url=$1]$2[/url]')
     */
    addHtmlToBbcodeTransformer(htmlExpression, bbcodeExpression) {
        this._htmlToBbcodeTransformer[htmlExpression] = bbcodeExpression;
    }

    /**
     * @returns {Object}
     */
    getHtmlToBbcodeTransformer() {
        return this._htmlToBbcodeTransformer;
    }
}

export default (new BBcodeTransformerManager)
