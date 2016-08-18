# GET CURRENT LOCALE
getCurrentLocale = ->
  $('#contextual-informations').data 'currentLanguage'

# SHOW CONTENT TITLE
renderPageTitle = ->
  if ($('#left-panel nav li.active:first > a > i').length > 0)
    setPageLogo($('#left-panel nav li.active:first > a > i').attr('class').replace('fa-lg', ''))
  $('#title-universe').text($('.breadcrumb li:nth-child(2)').text())
  $('#title-functionnality').text('> ' + $('.breadcrumb li:last').text()) if $('.breadcrumb li').length > 2

setPageLogo = (logo) ->
  $('#title-logo').addClass(logo)

# ADD CUSTOM JARVIS WIDGET
addCustomJarvisWidget = (newWidget, container) ->
  jarvisToolbar = $('.js-widget-title', container).parent()
  if $(newWidget).attr('data-widget-index')?
    indexWidget = $(newWidget).attr('data-widget-index')
    for widget in $(jarvisToolbar).children('.widget-toolbar')
      if $(widget).attr('data-widget-index')? and parseInt($(widget).attr('data-widget-index')) > indexWidget
         $(newWidget).insertBefore($(widget))
         return
  jarvisToolbar.append($(newWidget))
  return

# DISPLAY LOADER
displayLoader = (element, context) ->
  element = "#content"  if typeof element is "undefined"
  selector = if context? then $(element, context) else $(element)
  selector.html "<h1 class='loader'><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
  true

# REFRESH NAV MENU
opts =
  accordion: true
  speed: $.menu_speed
  closedSign: "<em class=\"fa fa-plus-square-o\"></em>"
  openedSign: "<em class=\"fa fa-minus-square-o\"></em>"
$("#left-panel nav").data({opts : opts})

#ADD DATATABLE PARAMETERS
if $('#left-panel nav').length > 0
  $.ajax
    url: $('#left-panel nav').data('datatable-parameter')
    type: 'GET'
    success: (response) ->
      window.dataTableConfigurator.setDataTableParameters(response)
      return

refreshMenu = (route, refresh) ->
  if $('#left-panel nav').length > 0
    $.ajax
      url: $('#left-panel nav').data('datatable-parameter')
      type: 'GET'
      success: (response) ->
        window.dataTableConfigurator.setDataTableParameters(response)
        displayMenu(route, refresh)
        return
    return

displayMenu = (route, refresh) ->
  selectedPath = "#" + (route || Backbone.history.fragment)
  refresh = refresh || (typeof route == "undefined")

  $.ajax
    url: $("#left-panel nav").data("url")
    type: "GET"
    success: (response) ->
      # render html
      opts = $("#left-panel nav").data('opts')
      $("#left-panel nav").replaceWith response
      $("#left-panel nav").data({opts : opts})

      # create the jarvis menu
      $("#left-panel nav ul").jarvismenu $("#left-panel nav").data('opts')

      # activate order node
      activateOrderNode()

      # tag selected path
      $("#left-panel nav li:has(a[href=\"" + selectedPath + "\"])").addClass "active"

      # open selected path
      openMenu($("#left-panel nav").data('opts').speed, $("#left-panel nav").data('opts').openedSign)

      if not refresh
        Backbone.history.navigate route,
          trigger: true

      return

  return

openMenu = (speed, openedSign) ->
  $("#left-panel nav").find("li.active").each ->
    $(this).parents("ul").slideDown speed
    $(this).parents("ul").parent("li").find("b:first").html openedSign
    $(this).parents("ul").parent("li").addClass "open"
    return


# SMARTADMIN CONFIRMATION
smartConfirm = (logo, titleColorized, text, functions) ->
  new SmartConfirmView(
    titleColorized: titleColorized
    logo: logo
    text: text
    buttons: [
      {
        text: 'Yes'
        callBack: if typeof functions.yesCallback != 'undefined' then functions.yesCallback else (->
        )
      }
      {
        text: 'No'
        callBack: if typeof functions.noCallback != 'undefined' then functions.noCallback else (->
        )
      }
    ]
    callBackParams: functions.callBackParams)
  return

selectorExist = (selector) ->
  return selector.length

#LOAD EXTEND VIEW
loadExtendView = (view, extendViewName) ->
  $.extend true, view, extendView[extendViewName]
  view.delegateEvents()

#CONFIGURATION LISTENER
$(document).on 'click', '.configuration-change', (e) ->
  target = $(e.currentTarget)
  url = target.data('url')
  window.location = url + '#' + Backbone.history.fragment

#LAUNCH SMARTADMIN NOTIFICATION
launchNotification = (type, message) ->
  iconClass = type
  color = '#305d8c'
  switch type
    when 'error'
      color = "#C26565"
      iconClass = "times"
    when 'warning'
      color = "#826430"
    when 'success'
      color = "#356635"
      iconClass = 'check'
  $.smallBox
    title: '<i class="fa-fw fa fa-' + iconClass + '"></i>'
    content: message
    color: color
    timeout: 4000

#SMARTADMIN RESET LOCAL STORAGE OVERRIDE
$.root_ && $.root_.on 'click', '[data-action="orchestraResetWidgets"]', (e) ->
  callbacks = {}
  callbacks.yesCallback = ->
    if localStorage
      localStorage.clear()
      location.reload()
    return
  smartConfirm('fa-refresh', $(e.currentTarget).data('message-title'), $(e.currentTarget).data('message-text'), callbacks)

#SMARTADMIN:JARVISMENU PREVENT LOAD PAGE ON EXTENDS ACCORDION MENU CALLS
$('#left-panel').on 'click', 'nav .collapse-sign em[class*=\'fa-\'][class*=\'-square-o\']', (event) ->
  event.preventDefault()
