$('#left-panel').on 'click', '.ajax-new', (e) ->
  e.preventDefault()
  createNew $(this)

createNew = (leaf) ->
  adminFormViewClass = appConfigurationView.getConfiguration(leaf.data('type'), 'showAdminForm')
  new adminFormViewClass(
    url: leaf.data("url")
    extendView: [ 'generateId' ]
    title: leaf.text( )
    entityType: leaf.data('type')
  )
  return
