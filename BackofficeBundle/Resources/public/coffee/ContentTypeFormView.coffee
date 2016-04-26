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
    optionId = targetId.replace(/type$/g, 'options')
    defaultValueId = targetId.replace(/type$/g, 'default_value')
    displayLoader('#' + optionId)
    $('#' + defaultValueId, @$el).closest( ".form-group" ).hide()
    $('#' + defaultValueId, @$el).val('')

    $('form', @$el).ajaxSubmit
      type: 'PATCH'
      success: (response) ->
        $('#' + optionId, viewContext.$el).parent().html if $('#' + optionId, response).length > 0 then $('#' + optionId, response).parent().html() else ''
        $('#' + defaultValueId, viewContext.$el).parent().html if $('#' + defaultValueId, response).length > 0 then $('#' + defaultValueId, response).parent().html() else ''
        $('#' + defaultValueId, viewContext.$el).closest( ".form-group" ).show()
        activateForm(viewContext, $('#' + defaultValueId, viewContext.$el).parent())
        activateForm(viewContext, $('#' + optionId, viewContext.$el).parent())

jQuery ->
  appConfigurationView.setConfiguration('content_types', 'editEntity', OpenOrchestra.ContentTypeFormView)
  appConfigurationView.setConfiguration('content_types', 'addEntity', OpenOrchestra.ContentTypeFormView)
