refreshUl = (ul) ->
  if ul isnt null
    childs = ul.children(':visible')
    nbrChildren = childs.length
    direction = if childs.filter(".block").length > 0 then "height" else "width"
    childs.each ->
      $(this).css direction, (100 / nbrChildren) + "%"
      return
  return
makeSortable = (el, duplicate) ->
  $("ul.ui-model-blocks", el).sortable(
    connectWith: "#content ul.ui-model-blocks",
    appendTo: 'body',
    tolerance: 'pointer',
    helper: (event, obj) ->
      inHelper = obj.clone()
      inHelper.css 'width', '100%'
      inHelper.css 'height', '100%'
      $("<div></div>").addClass("ui-model").append inHelper
    create: (event, ui)->
      @stockedUl = $(this)
      @duplicate = duplicate
      @evaluateRefreshable = ->
        not @duplicate or not $(this).is(@stockedUl)
    start: (event, ui)->
      if @duplicate
        ui.placeholder.hide()
        ui.item.show()
      ui.placeholder.css "width", (100 * ui.item.width() / $(this).width()) + "%"
      ui.placeholder.css "height", (100 * ui.item.height() / $(this).height()) + "%"
    change: (event, ui) ->
      refreshUl @stockedUl  if @evaluateRefreshable()
      @stockedUl = ui.placeholder.parent()
      refreshUl @stockedUl  if @evaluateRefreshable()
      ui.helper.height ui.placeholder.height()
      ui.helper.width ui.placeholder.width()
      if @evaluateRefreshable()
        ui.placeholder.show()
      else
        ui.placeholder.hide()
        ui.helper.css "width", ui.item.width()
        ui.helper.css "height", ui.item.height()
  ).disableSelection()
  return
