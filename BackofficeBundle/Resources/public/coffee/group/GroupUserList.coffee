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
    displayLoader('.table-container', @$el)

    table = @getDisplayListUser()
    $('.table-container', @$el).html table

    @$el.append @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/backToList',
      listUrl : @options.listUrl
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
      page: if @options.page? then parseInt(@options.page) - 1 else 0
      serverSide: false
      globalSearch: false
      searching: false
      paging: false
      buttons: []
      dom: 't'
      processing: false
      columnDefs: columnDefs
      columns: columns
      tableId: @options.entityType
      tableClassName: 'table table-striped table-bordered table-hover smart-form'
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
      tableId: @options.entityType
      domContainer : $(td)
    ))

jQuery ->
  appConfigurationView.setConfiguration 'group_tab_user_list', 'editEntityTab', OpenOrchestra.Group.GroupUserList
