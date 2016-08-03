###*
 * @namespace OpenOrchestra:Group
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Group or= {}

###*
 * @class GroupUserList
###
class OpenOrchestra.Group.GroupUserList extends OrchestraView

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = options
    @options.listUrl = appRouter.generateUrl('listEntities', entityType: options.entityType) if options.listUrl == undefined
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/group/groupUserList',
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
    ]
    return

  ###*
   * Render user list
  ###
  render: ->
    @$el.html @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/group/groupUserList')
    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
      listUrl : @options.listUrl
    )

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_user_list', 'editEntityTab', OpenOrchestra.Group.GroupUserList
