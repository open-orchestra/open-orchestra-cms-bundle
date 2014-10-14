$('#left-panel').on 'click', '.ajax-new', (e) ->
  e.preventDefault()
  showNodeForm $(this)
