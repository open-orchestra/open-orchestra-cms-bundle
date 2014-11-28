makeDroppable = ->
  $('#droppable').droppable
    accept: "#draggable"
    hoverClass: "trash-red-color"
    drop: (event, ui) ->
      $(this).addClass("ui-state-highlight")
  return
