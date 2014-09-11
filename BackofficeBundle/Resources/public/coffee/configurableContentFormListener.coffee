$("#OrchestraBOModal").on "change", ".contentTypeSelector", (e) ->
  contentTypeId = $(this).val()
  url = $(this).data("url")
  alert(contentTypeId)
  $.ajax
    type: "GET"
    url: url
    data:
      content_type: contentTypeId
    success: (response) ->
      $("#block_contentId").find("option").remove()
      $.each response.contents, (index, item) ->
        $("#block_contentId").append new Option(item["name"], item["content_id"])
        return
      return
  return
