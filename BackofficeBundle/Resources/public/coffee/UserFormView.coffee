###*
 * @namespace OpenOrchestra
###
window.OpenOrchestra or= {}

###*
 * @class UserFormView
###
class OpenOrchestra.UserFormView extends FullPageFormView

  ###*
   * On view ready
  ###
  onViewReady: ->
    success = $('.alert-success', @$el).length > 0
    if @options.submitted && success
      entityId = $('#oo_user', @$el).first().data('user-id')
      parameters =
        'entityType': @options.entityType
        'entityId': entityId
      redirectUrl = appRouter.generateUrl('showEntity', parameters)
      Backbone.history.navigate redirectUrl,
        trigger: true

jQuery ->
  appConfigurationView.setConfiguration('user', 'editEntity', OpenOrchestra.UserFormView)
