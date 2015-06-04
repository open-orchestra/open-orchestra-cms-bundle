extendView = extendView || {}
extendView['contentTypeSelector'] = {
  events:
    'change .contentTypeSelector': 'changeContentTypeSelector'

  changeContentTypeSelector: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    contentTypeId = target
    url = target.data("url")
    $.ajax
      type: "GET"
      url: url
      data:
        content_type: contentTypeId
      success: (response) ->
        $("#block_contentId").find("option").remove()
        $.each response.contents, (index, item) ->
          $("#block_contentId").append new Option(item["name"], item["id"])
          return
        return
    return
}
