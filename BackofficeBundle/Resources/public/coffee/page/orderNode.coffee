$("ul.node-connectedSortable").sortable
  connectWith: "ul.node-connectedSortable"

$("ul.node-connectedSortable").on "sortupdate", (event)->
  ul = $(event.target)
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