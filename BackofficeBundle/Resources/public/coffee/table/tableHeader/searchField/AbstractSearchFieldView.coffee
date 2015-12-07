AbstractSearchFieldView = OrchestraView.extend(

  searchColumn : (event) ->
    value = $(event.target).val()
    columnIndex = $(event.target).closest("td").get(0).cellIndex
    api = @options.table.api()
    api.column(columnIndex+':visible').search(value).draw()

  insertFieldInHeader : () ->
      for td in @options.domContainer.children('td')
        if parseInt($(td).data('column')) > @options.column
          @$el.insertBefore($(td))
          return
      @options.domContainer.append(@$el)
)
