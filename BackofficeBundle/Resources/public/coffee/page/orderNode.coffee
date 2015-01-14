$("ul.node-connectedSortable").sortable
  connectWith: "ul.node-connectedSortable"
  cancel: ".node-unsortable"
  beforeStop: (event, ui)->
    smartConfirm(
      'fa-sort-amount-asc',
      $(this).data('confirm-title') + ' "' + $(event.toElement).text() + '"',
      $(event.toElement).parent().parent().parent().data('confirm-text'),
      callBackParams:
        sortableElement: $(this)
      noCallback: (params) ->
        params.sortableElement.sortable("cancel")
    )

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
