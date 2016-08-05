###*
 * @namespace OpenOrchestra:Group
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Group or= {}

###*
 * @class TableUserListActionView
###
class OpenOrchestra.Group.TableUserListActionView extends OrchestraView

  events:
    'click a.ajax-delete': 'clickDelete'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = options
    _.bindAll this, "render"
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/group/tableUserListAction'
    ]
    return

  ###*
   * Render list action
  ###
  render: ->
    @setElement $('<p />')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/group/tableUserListAction',
      links: @options.element.get('links')
    )
    @options.domContainer.html(@$el)

  ###*
   * Delete user of group
  ###
  clickDelete: (event) ->
    event.preventDefault()
    options = @options
    smartConfirm(
      'fa-trash-o',
      $(event.currentTarget).data('title'),
      $(event.currentTarget).data('text'),
      callBackParams:
        url: @options.element.get('links')._self_delete
        row: $(event.target).closest('tr')
      yesCallback: (params) ->
        params.row.hide()
        OpenOrchestra.DataTable.Channel.trigger 'clearCache', options.tableId
        $.ajax
          url: params.url
          method: 'DELETE'
          complete: () ->
            OpenOrchestra.DataTable.Channel.trigger 'draw', options.tableId
            OpenOrchestra.Table.Channel.trigger 'removeEntity', options.tableId
    )

jQuery ->
  appConfigurationView.setConfiguration 'group', 'groupRemoveUserButton',  OpenOrchestra.Group.TableUserListActionView
