refreshUl = (ul) ->
  if ul isnt null
    children = ul.children(':visible')
    placeholder = children.filter(".ui-sortable-placeholder")
    if placeholder.length > 0
      placeholder.addClass "ui-sortable-placeholder"
      placeholder.html '<div class="preview"></div>'

makeSortable = (el, isDuplicable) ->
  handler = '.move-tool'
  handler = false if $(el).parent().hasClass('js-widget-blockpanel')
  $(".ui-model-blocks", el).sortable(
    connectWith: '#content div[role="container"] .ui-model-blocks',
    handle: handler,
    appendTo: 'body',
    tolerance: 'pointer',
    forceHelperSize: 'true',
    zIndex: 10000,
    helper: (event, obj) ->
      $("<div></div>").addClass("ui-model drag-helper").append obj.clone()
    create: (event, ui)->
      @clone = $(this).clone() if isDuplicable
    start: (event, ui)->
      ui.item.show()  if isDuplicable
    stop: (event, ui)->
      if isDuplicable and @clone
        $(this).replaceWith(@clone)
        makeSortable el, true
  )
