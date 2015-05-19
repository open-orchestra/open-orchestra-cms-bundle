userPanelLoad = (link,userId, language, version) ->
  title = link.text()
  $.ajax
    url: link.data("url") + "/" + userId
    method: 'GET'
    success: (response) ->
      userModel = new PanelModel()
      userModel.set response
      new userPanelView(user: userModel)
  return
