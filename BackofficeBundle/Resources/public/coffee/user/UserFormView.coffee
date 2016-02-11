###*
 * @namespace OpenOrchestra:User
###
window.OpenOrchestra or= {}
window.OpenOrchestra.User or= {}

###*
 * @class UserFormView
###
class OpenOrchestra.User.UserFormView extends FullPageFormView

  ###*
   * On view ready
  ###
  onViewReady: ->
    success = $('.alert-success', @$el).length > 0
    if @options.submitted && success
      parameters =
        'entityType': @options.entityType
        'entityId': $('#oo_user_id', @$el).val()
      redirectUrl = appRouter.generateUrl('showEntity', parameters)
      refreshMenu(redirectUrl)

jQuery ->
  appConfigurationView.setConfiguration('user', 'editEntity', OpenOrchestra.User.UserFormView)
