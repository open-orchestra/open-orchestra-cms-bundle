stockedUl = null
refreshUl = (ul) ->
  childs = ul.children().not(".ui-sortable-helper")
  nbrChildren = childs.length
  direction = ((if nbrChildren > 0 and ul.width() is childs.first().width() then "height" else "width"))
  childs.each ->
    $(this).css direction, (100 / nbrChildren) + "%"
    return
  return
makeSortable = (el) ->
  $("ul.ui-model-blocks", el).sortable(
    connectWith: "#content ul.ui-model-blocks",
    appendTo: 'body',
    helper: 'clone',
    start: (event, ui)->
      ui.placeholder.css "width", (100 * ui.item.width() / $(this).width()) + "%"
      ui.placeholder.css "height", (100 * ui.item.height() / $(this).height()) + "%"
    change: (event, ui) ->
      refreshUl stockedUl  if stockedUl isnt null
      stockedUl = ui.placeholder.parent()
      refreshUl stockedUl
      ui.helper.height ui.placeholder.height()
      ui.helper.width ui.placeholder.width()
  ).disableSelection()
  return
makeSortable ".blocks-panel"
