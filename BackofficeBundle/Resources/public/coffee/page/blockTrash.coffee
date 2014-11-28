makeDroppable = ->
  $('#droppable').droppable
    accept: "#draggable"
    hoverClass: "trash-red-color"
    drop: (event, ui) ->
#      remove block
  return
