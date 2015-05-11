refreshUl = (ul) ->
  if ul isnt null
    childs = ul.children(':visible')
    placeholder = childs.filter(".ui-sortable-placeholder")
    if placeholder.length > 0
      applyClass placeholder
      placeholder.addClass "ui-sortable-placeholder"
      placeholder.html '<div class="preview"></div>'
    return
  return


makeSortable = (el) ->
  handler = '.move-tool'
  handler = false if $(el).parent().hasClass('js-widget-blockpanel')
  $(".ui-model-blocks", el).sortable
    connectWith: '#content div[role="container"] .ui-model-blocks'
    handle: handler
    forceHelperSize: true
  $(handler).disableSelection()
  return

