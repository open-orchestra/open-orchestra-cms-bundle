stockedUl = null
refreshUl = (ul) ->
  childs = ul.children(':visible')
  nbrChildren = childs.length
  direction = if childs.filter(".block").length > 0 then "height" else "width"
  childs.each ->
    $(this).css direction, (100 / nbrChildren) + "%"
    return
  return
makeSortable = (el) ->
  $("ul.ui-model-blocks", el).sortable(
    connectWith: "#content ul.ui-model-blocks",
    appendTo: 'body',
    tolerance: 'pointer',
    helper: (event, obj) ->
      inHelper = obj.clone()
      inHelper.css 'width', '100%'
      inHelper.css 'height', '100%'
      $("<div></div>").addClass("ui-model").append inHelper
    start: (event, ui)->
      ui.placeholder.css "width", (100 * ui.item.width() / $(this).width()) + "%"
      ui.placeholder.css "height", (100 * ui.item.height() / $(this).height()) + "%"
      stockedUl = ui.item.parent()
    change: (event, ui) ->
      refreshUl stockedUl  if stockedUl isnt null
      stockedUl = ui.placeholder.parent()
      refreshUl stockedUl
      ui.helper.height ui.placeholder.height()
      ui.helper.width ui.placeholder.width()
  ).disableSelection()
  return
makeSortable ".blocks-panel"
