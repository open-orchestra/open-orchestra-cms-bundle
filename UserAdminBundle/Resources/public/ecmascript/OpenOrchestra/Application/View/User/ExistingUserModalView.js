import ModalView     from '../../../Service/Modal/View/ModalView'

/**
 * @class ExistingUserModalView
 */
class ExistingUserModalView extends ModalView
{
    /**
     * @inheritdoc
     */
    preinitialize() {
        super.preinitialize();
        this.events['click .edit-user'] =  '_removeModal'
    }

    /**
     * Initialize
     *
     * @param {User} user
     */
    initialize({user}) {
        this._user = user;
    }

    /**
     * Render error
     */
    render() {
        let template = this._renderTemplate('User/existingUserModalView', {
            user: this._user
        });
        this.$el.html(template);

        return this;
    }

    /**
     * Remove and hide modal
     *
     * @private
     */
    _removeModal() {
        this.$el.modal('hide');
    }
}

export default ExistingUserModalView;
