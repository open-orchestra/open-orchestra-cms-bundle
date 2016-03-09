###*
 * @namespace OpenOrchestra:GroupTree
###
window.OpenOrchestra or= {}
window.OpenOrchestra.GroupTree or= {}

###*
 * @class AbstractFormCollectionView
###
class OpenOrchestra.GroupTree.AbstractFormCollectionView extends OrchestraView

  events:
    'change .value-holder': 'changeInput'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    ###*
     * {Object} domContainer
     * {Object} @getElement()
     * {Array}  @getGroupRoles()
     * {Object} group
     * {Array}  roles
    ###
    @options = @reduceOption(options, [
      'domContainer'
      @getElement(),
      @getGroupRoles(),
      'group'
      'roles'
    ])
    @loadTemplates [
      'OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/groupTreeForm',
    ]

  ###*
   * render
  ###
  render: ->
    for role in @options.roles
      @options.domContainer.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/groupTree/groupTreeForm',
        role: role
        element: @options[@getElement()]
      )
    @setElement @options.domContainer
    if @options[@getGroupRoles()] != undefined
      for groupRole in @options[@getGroupRoles()]
        $('select[data-role-name="' + groupRole.name + '"] option[value="' + groupRole.access_type + '"]', @options.domContainer).attr('selected','selected')

  ###*
   * @param {Object} e
  ###
  changeInput: (e) ->
    target = $(e.currentTarget);
    target.hide()
    loader = $('<i class=\"fa fa-cog fa-spin\"></i>')
    loader.insertAfter(target)
    value = target.val()
    name = target.data('role-name')
    id = @options[@getElement()][@getId()]
    groupRoleData = []
    groupRoleData.push({'model_id': id, 'type':@getType(), 'access_type': value, 'name': name})
    $.ajax
      url: @options.group.links._self_edit
      method: 'POST'
      data: JSON.stringify(
        model_roles: groupRoleData
      )
      success: (response) ->
        loader.remove()
        target.show()

