(($, OpenOrchestra) ->

  ###*
   * @class BooleanFieldSearchView
  ###
  class BooleanFieldSearchView extends AbstractSearchFieldView

    events:
      'change select.search-column': 'searchColumn'

    ###*
     * required options
     * {
     *   api: {object} DataTable api
     *   domContainer: {object} jquery element
     *   column: {integer} column index
     * }
     * @param {Object} options
    ###
    initialize: (options) ->
      @options = @reduceOption(options, [
        'api'
        'domContainer'
        'columnIndex'
      ])
      @loadTemplates ['OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/booleanField']

    ###*
     * @return {this}
    ###
    render: ->
      @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/datatable/header/booleanField',
          column : @options.columnIndex
      )
      $('.search-column', @$el).val(@options.api.column(@options.columnIndex).search())
      @insertFieldInHeader()

      return @

  OpenOrchestra.DataTable = {} if not OpenOrchestra.DataTable?
  OpenOrchestra.DataTable.ViewFieldConfigurator = {} if not OpenOrchestra.DataTable.ViewFieldConfigurator?
  OpenOrchestra.DataTable.ViewFieldConfigurator.boolean = BooleanFieldSearchView

) jQuery,
  window.OpenOrchestra = window.OpenOrchestra or {}
