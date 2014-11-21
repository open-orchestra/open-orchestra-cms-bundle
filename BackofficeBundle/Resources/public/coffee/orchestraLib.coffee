# GET CURRENT LOCALE
getCurrentLocale = ->
  $('#contextual-informations').data 'currentLanguage'


# SHOW CONTENT TITLE

renderPageTitle = ->
  if ($('nav li.active:first > a > i').length > 0)
    $('#title-logo').addClass($('nav li.active:first > a > i').attr('class').replace('fa-lg', ''))
  $('#title-universe').text($('.breadcrumb li:nth-child(2)').text())
  $('#title-functionnality').text('> ' + $('.breadcrumb li:last').text())


# ADD CUSTOM JARVIS WIDGET

addCustomJarvisWidget = (widget) ->
  $(widget).insertAfter($(".js-widget-title"))
  return


# CHANGE NODE STATUS

nodeChangeStatus = (url, newStatusId) ->
  displayLoader()
  data =
    status_id: newStatusId
  data = JSON.stringify(data)
  $.post(url, data).always (response) ->
    Backbone.history.loadUrl(Backbone.history.fragment)
    return
  return


# DISPLAY LOADER

displayLoader = (element) ->
  element = "#content"  if typeof element is "undefined"
  $(element).html "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
  true


# CALL A URL TO CHANGE SOMETHING IN THE CONTEXT
# AND RELOAD HOMEPAGE

callAndReload = (action) ->
  displayLoader()
  $.post action, (response) ->
    if response.success
      Backbone.history.navigate "#", true
      window.location.reload()
    return

  return


# REFRESH NAV MENU

displayMenu = (route) ->
  selectedPath = undefined
  if typeof route isnt "undefined"
    selectedPath = "#" + route
  else
    selectedPath = "#" + Backbone.history.fragment
  $.ajax
    url: $("#left-panel nav").data("url")
    type: "GET"
    success: (response) ->
      
      # render html
      $("#left-panel nav").replaceWith response
      
      # create the jarvis menu
      opts =
        accordion: true
        speed: $.menu_speed
        closedSign: "<em class=\"fa fa-expand-o\"></em>"
        openedSign: "<em class=\"fa fa-collapse-o\"></em>"

      $("nav ul").jarvismenu opts
      
      # tag selected path 
      $("nav li:has(a[href=\"" + selectedPath + "\"])").addClass "active"
      
      # open selected path
      $("#left-panel nav").find("li.active").each ->
        $(this).parents("ul").slideDown opts.speed
        $(this).parents("ul").parent("li").find("b:first").html opts.openedSign
        $(this).parents("ul").parent("li").addClass "open"
        return

      if typeof route isnt "undefined"
        Backbone.history.navigate route,
          trigger: true

      return

  return


# AJAX LOADER

orchestraAjaxLoad = (url, method, successCallback) ->
  displayLoader()
  method = "POST"  if typeof method is "undefined"
  $.ajax
    url: url
    type: method
    success: (response) ->
      if response.success
        window.location.hash = response.data
      else
        $("#content").html response
        successCallback()  if typeof successCallback isnt "undefined"
      return

  return
