###*
 * @namespace OpenOrchestra:Table
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Table or= {}

###*
 * @class TableviewTrashcanButtonAction
###
class OpenOrchestra.Table.TableviewTrashcanButtonAction extends OrchestraView
  events:
    'click a.ajax-restore': 'clickRestore'
    'click a.ajax-remove': 'clickRemove'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewTrashcanButtonAction'
    ]
    return

  ###*
   * Render view
  ###
  render: ->
    @setElement $('<p />')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/table/tableviewTrashcanButtonAction',
      links: @options.element.get('links')
    )
    @options.domContainer.html(@$el)

  ###*
   * @param {Object} event
  ###
  clickRestore : (event) ->
    event.preventDefault()
    options = @options
    smartConfirm(
      'fa-undo',
      $('a',@$el).data('restore-confirm-title'),
      $('a',@$el).data('restore-confirm-txt'),
    callBackParams:
        url: @options.element.get('links')._self_restore
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        OpenOrchestra.DataTable.Channel.trigger 'clearCache', options.tableId
        $.ajax
          url: params.url
          method: 'PUT'

          success : () ->
            params.row.hide()
            OpenOrchestra.DataTable.Channel.trigger 'draw', options.tableId
            displayRoute = appRouter.generateUrl "listEntities",
              entityType: options.entityType
            refreshMenu(displayRoute)
    )

  ###*
   * @param {Object} event
  ###
  clickRemove: (event) ->
    event.preventDefault()
    options = @options
    smartConfirm(
      'fa-trash-o',
      $(event.currentTarget).data('remove-confirm-title'),
      $(event.currentTarget).data('remove-confirm-txt')
      callBackParams:
        url: @options.element.get('links')._self_remove
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        OpenOrchestra.DataTable.Channel.trigger 'clearCache', options.tableId
        $.ajax
          url: params.url
          method: 'DELETE'
          success : () ->
            params.row.hide()
            OpenOrchestra.DataTable.Channel.trigger 'draw', options.tableId
    )


jQuery ->
  appConfigurationView.setConfiguration('trashcan', 'addButtonAction', OpenOrchestra.Table.TableviewTrashcanButtonAction)
