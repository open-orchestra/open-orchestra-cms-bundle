activateOrderNode = () ->
  $("ul.node-connectedSortable").sortable
    connectWith: "ul.node-connectedSortable"
    cancel: ".node-unsortable"
    delay: 150
    distance: 5
    activate: (event, ui)->
      @target = $(event.originalEvent.target)
    beforeStop: (event, ui)->
      if(@target.is($(event.originalEvent.target)))
        smartConfirm(
          'fa-sort-amount-asc',
          $(this).data('confirm-title') + ' "' + $(event.originalEvent.target).text() + '"',
          $(event.originalEvent.target).closest('ul.node-connectedSortable').data('confirm-text')
          callBackParams:
            sortableElement: $(this)
          noCallback: (params) ->
            params.sortableElement.sortable("cancel")
          yesCallback: (params) ->
            sendSortedNodeData(params.sortableElement)
            sendSortedNodeData($(ui.item).parent()) if params.sortableElement[0] != $(ui.item).parent()[0]
        )
sendSortedNodeData = (ul) ->
  nodes = ul.children('li[data-type="Node"]')
  nodeData = []
  for node in nodes
    nodeData.push({'node_id': $(node).data('element-id')})
  nodeCollection = {}
  nodeCollection['nodes'] = nodeData
  $.ajax
    url: ul.data('update-order')
    method: 'POST'
    data: JSON.stringify(nodeCollection)

jQuery ->
  activateOrderNode()
  return
