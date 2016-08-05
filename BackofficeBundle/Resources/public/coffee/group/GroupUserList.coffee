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
    @options.tableId = @options.entityType+'_list_user'
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
    displayLoader('.table-container', @$el)

    table = @getDisplayListUser()
    $('.table-container', @$el).html table

    @initSelectAddUser($('.add-user-select2', @$el), $('.table-container table', @$el))

    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
      listUrl : @options.listUrl
    )

  ###*
   * Init input to add an user in group
   *
   * @param {Object} selector
   * @param {Object} table datatable instance
  ###
  initSelectAddUser: (selector, table) ->
    viewContainer = @
    selector.select2(
      minimumInputLength: 1,
      ajax:
        url: @options.response.links._list_without_group,
        dataType: 'json',
        quietMillis: 250,
        cache: true
        data: (term) ->
          return {username: term}
        results: (data) ->
          return {results: data.users}
      formatResult: (result, container) ->
        $(container).click({user: result, table: table, selector: selector}, viewContainer.addUser)
        return result.username + ' (' + result.email + ')'
      formatSelection: (result) ->
        result.username
      formatResultCssClass: () ->
        return 'list-group-item'
      dropdownCssClass: 'list-group'
    )

  ###*
   * Click on a user in select2 result
   *
   * @param {Object} event
  ###
  addUser: (event) ->
    displayLoader($(event.target))
    user = event.data.user
    table = event.data.table
    inputSelect2 = event.data.selector
    url = user.links._self_add
    $.ajax(
      method: 'POST'
      url: url
      success: () ->
        table.DataTable().row.add(user).draw()
      complete: () ->
        inputSelect2.select2('close')
    )

  ###*
   * generate datatable with user data
  ###
  getDisplayListUser: () ->
    columns = []
    columnDefs = []
    viewContext = @
    columnsElement = $('.table-container', @$el).attr('data-column').replace(/\s/g, '').split(',')
    translatedHeader = $('.table-container', @$el).attr('data-translatedHeader').replace(/\s/g, '').split(',')
    for index, name of columnsElement
      columns.push({'data' : name, 'defaultContent': ''});
      columnDefs.push({
        targets: columnDefs.length
        name: name
        title: translatedHeader[index]
      });
    columns.push({'data' : 'links'})
    columnDefs.push(
      targets: -1
      data: 'links'
      orderable: false
      createdCell : (td, cellData, rowData, row, col) ->
        viewContext.renderColumnActions(viewContext, td, cellData, rowData, row, col)
    )

    datatableViewClass = appConfigurationView.getConfiguration(@options.entityType,'addDataTable')
    datatable = new datatableViewClass(
      serverSide: false
      globalSearch: false
      searching: false
      paging: false
      buttons: []
      dom: 't'
      processing: false
      columnDefs: columnDefs
      columns: columns
      tableId: @options.tableId
      tableClassName: 'table table-striped table-bordered table-hover smart-form'
      stateSave: false
      language:
        url: appRouter.generateUrl('loadTranslationDatatable')
      data: @options.response.users
    );
    table = datatable.$el

    return table

  ###*
   * render column action with remove button
  ###
  renderColumnActions : (viewContext, td, cellData, rowData) ->
    elementModel = new TableviewModel
    elementModel.set rowData
    tableActionViewClass = appConfigurationView.getConfiguration(viewContext.options.entityType, 'groupRemoveUserButton')
    new tableActionViewClass(viewContext.addOption(
      element: elementModel
      tableId: @options.tableId
      domContainer : $(td)
    ))

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_user_list', 'editEntityTab', OpenOrchestra.Group.GroupUserList
