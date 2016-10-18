/**
 * @class Form
 */
class Form
{
    /**
     * Constructor
     *
     * @param {string} $html
     * @param {string} method
     * @param {string} url
     */
    constructor($html, method, url) {
        this.$html = $html;
        this.method = method;
        this.url = url;
        this.data = {};
    }
}

export default Form;