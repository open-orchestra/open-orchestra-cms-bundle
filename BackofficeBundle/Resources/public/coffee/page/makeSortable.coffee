applyClass = (li) ->
  li.attr('class', li.siblings().attr('class'))
  li.css('display', '')

refreshUl = (ul) ->
  if ul isnt null
    childs = ul.children(':visible')
    placeholder = childs.filter(".ui-sortable-placeholder")
    if placeholder.length > 0
      applyClass placeholder
      placeholder.addClass "ui-sortable-placeholder"
      placeholder.html '<div class="preview"></div>'
    nbrChildren = childs.length
    direction = if ul.filter(".bo-row").length > 0 then "width" else "height"
    childs.each ->
      $(this).css direction, (100 / nbrChildren) + "%"
      $(this).css (if direction is "height" then "width" else "height"), "auto"
      return
  return

makeSortable = (el, duplicate) ->
  handler = '.move-tool'
  handler = false if $(el).parent().hasClass('js-widget-blockpanel')
  $(".ui-model-blocks", el).sortable(
    connectWith: '#content div[role="container"] .ui-model-blocks',
    handle: handler,
    appendTo: 'body',
    tolerance: 'pointer',
    zIndex: 100000,
    helper: (event, obj) ->
      $("<div></div>").addClass("ui-model drag-helper").append obj.clone()
    create: (event, ui)->
      @stockedUl = $(this)
      @duplicate = duplicate
      @clone = $(this).clone() if @duplicate
      @evaluateRefreshable = ->
        not @duplicate or not $(this).is(@stockedUl)
      @refreshHelper = (ui) ->
        refreshUl @stockedUl  if @evaluateRefreshable()
        placeholder = ui.placeholder
        @stockedUl = ui.placeholder.parent()
        if @evaluateRefreshable()
          ui.placeholder.show()
          refreshUl @stockedUl
        else
          ui.placeholder.hide()
          placeholder = ui.item
        $('li', ui.helper).height placeholder.height()
        $('li', ui.helper).width placeholder.width()
    start: (event, ui)->
      ui.item.show()  if @duplicate
      @refreshHelper(ui)
    change: (event, ui) ->
      @refreshHelper(ui)
    stop: (event, ui)->
      applyClass ui.item
      if @duplicate and @clone
        $(this).replaceWith(@clone)
        makeSortable el, duplicate
  )
  return
