###*
 * @namespace OpenOrchestra
###
window.OpenOrchestra or= {}

###*
 * @class ContentTypeFormView
###
class OpenOrchestra.ContentTypeFormView extends FullPageFormView

  events:
    'change .content_type_change_type': 'changeContentTypeChange'

  ###*
   * Refresh the navigation when a content type is created
  ###
  onElementCreated: ->
    displayRoute = appRouter.generateUrl('listEntities', entityType: @options.entityType)
    refreshMenu(displayRoute, true)

  ###*
   * Refresh a field form setting when the type is changed
   *
   * @param {Object} event
  ###
  changeContentTypeChange: (event) ->
    event.preventDefault()
    viewContext = @
    targetId = $(event.currentTarget).attr('id')
    containerId = targetId.replace(/_type$/g, '')
    window.OpenOrchestra.FormBehavior.channel.trigger 'deactivate', @, $('#' + containerId, @$el)
    displayLoader('#' + containerId + '_options')
<<<<<<< HEAD
    $('[for="' + containerId + '_default_value"]', @$el).parent().remove()
=======
    label = $('[for="' + containerId + '_default_value"]', @$el)
    label.parent().remove()
>>>>>>> 5fd53d6e9c1a4d8b163c6a3de1e356c83f760e73
    $('form', @$el).ajaxSubmit
      url: $('form', @$el).data('action')
      type: 'PATCH'
      success: (response) ->
        $('#' + containerId, viewContext.$el).html $('#' + containerId, response).html()
        window.OpenOrchestra.FormBehavior.channel.trigger 'activate', viewContext, $('#' + containerId, viewContext.$el)

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', OpenOrchestra.ContentTypeFormView)
  appConfigurationView.setConfiguration('content_types', 'addEntity', OpenOrchestra.ContentTypeFormView)
