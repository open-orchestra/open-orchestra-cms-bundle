TableviewSearchHeader = OrchestraView.extend(

  tagName: "tr"
  className: "header-search-input"

  initialize: (options) ->
    @options = options
    @render()
    widgetChannel.trigger 'ready', @
    return

  render: ->
    for type, column in @options.inputHeader
        type = "empty" if not tableFieldViewConfigurator[type]?
        viewClass = tableFieldViewConfigurator[type]
        new viewClass(@addOption(
            column : column
            apiTable : @options.apiTable
            domContainer : @$el
        ))

    @options.domContainer.append(@$el)
)

tableFieldViewConfigurator = {
    "empty" : EmptySearchView
}