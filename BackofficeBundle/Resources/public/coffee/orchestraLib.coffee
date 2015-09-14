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

# DISPLAY LOADER
displayLoader = (element, context) ->
  element = "#content"  if typeof element is "undefined"
  selector = if context? then $(element, context) else $(element)
  selector.html "<h1><i class=\"fa fa-cog fa-spin\"></i> Loading...</h1>"
  true

# REFRESH NAV MENU
opts =
  accordion: true
  speed: $.menu_speed
  closedSign: "<em class=\"fa fa-plus-square-o\"></em>"
  openedSign: "<em class=\"fa fa-minus-square-o\"></em>"
$("nav").data({opts : opts})

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
      $("nav ul").jarvismenu $("nav").data('opts')
      
      # tag selected path 
      $("nav li:has(a[href=\"" + selectedPath + "\"])").addClass "active"
      
      # open selected path
      openMenu($("nav").data('opts').speed, $("nav").data('opts').openedSign)

      if typeof route isnt "undefined"
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

#SELECT2 ENABLED
activateSelect2 = (element) ->
  tags = element.data('tags')
  url = element.data('check')
  element.select2(
    tags: tags
    createSearchChoice: (term, data) ->
      if $(data).filter(->
        @text.localeCompare(term) is 0
      ).length is 0
        id: term
        text: term
        isNew: true
    formatResult: (term) ->
      if term.isNew
        $.ajax
          type: 'GET'
          url: url
          data: 'term=' + encodeURIComponent(term.text)
          success: (response) ->
            term.text = response.term
        "<span class=\"label label-danger\">New</span> " + term.text
      else
        term.text
    formatSelection: (term, container) ->
      container.parent().addClass('bg-color-red').attr('style', 'border-color:#a90329!important') if term.isNew
      term.text
  )

#NODE CHOICE ENABLED
activateOrchestraNodeChoice = (element) ->
  regExp = new RegExp('((\u2502|\u251C|\u2514)+)', 'g')
  $('option', element).each ->
    $(this).addClass 'orchestra-node-choice'
  element.select2(
    formatResult: (term) ->
      term.text.replace regExp, '<span class="hierarchical">$1</span>'
    formatSelection: (term) ->
      term.text.replace regExp, ''
  )

#COLORPICKER ENABLED
activateColorPicker = (element) ->
  element.minicolors()

#HELPER ENABLED
activateHelper = (element) ->
  element.tooltip()

#LOAD EXTEND VIEW
loadExtendView = (view, extendViewName) ->
  $.extend true, view, extendView[extendViewName]
  view.delegateEvents()

#CONFIGURATION LISTENER
$(document).on 'click', '.configuration-change', (e) ->
  target = $(e.currentTarget)
  url = target.data('url')
  window.location = url + '#' + Backbone.history.fragment

#ACTIVATE HTML5 VALIDATION FOR HIDDEN
activateHidden = (hidden) ->
  hidden.addClass('focusable').attr('type', 'text')

#ACTIVATE FORM JS
activateForm = (view, form) ->
  tinymce.editors = []
  activateSelect2(elements) if (elements = $(".select2", form)) && elements.length > 0
  activateOrchestraNodeChoice(elements) if (elements = $(".orchestra-node-choice", form)) && elements.length > 0
  activateColorPicker(elements) if (elements = $(".colorpicker", form)) && elements.length > 0
  activateHelper(elements) if (elements = $(".helper-block", form)) && elements.length > 0
  activateTinyMce(view, elements) if (elements = $("textarea.tinymce", form)) && elements.length > 0
  activateHidden(elements) if (elements = $("input[type='hidden'][required='required']", form)) && elements.length > 0
  $("[data-prototype]", form).each ->
    PO.formPrototypes.addPrototype $(@)
  loadExtendView(view, 'contentTypeSelector') if (elements = $(".contentTypeSelector", form)) && elements.length > 0
  loadExtendView(view, 'contentTypeChange') if (elements = $("[data-prototype*='content_type_change_type']", form)) && elements.length > 0

#UPDATE DEBUG BAR
updateDebugBar = (xhr) ->
  url = xhr.getResponseHeader('X-Debug-Token-Link')
  if url? and $('.sf-toolbar').length
    $.get url, (data) ->
      toolbarData = $(data).filter('.sf-toolbarreset')
      if toolbarData
        btn = $('.sf-toolbar .sf-toolbarreset .hide-button');
        $('.sf-toolbar .sf-toolbarreset').html(toolbarData.html()).append(btn)
  return

#LAUNCH SMARTADMIN NOTIFICATION
launchNotification = (type, message) ->
  if type == 'error'
    color = "#C26565"
    iconClass = "times"
  else
    color = "#826430"
    iconClass = type
  $.smallBox
    title: '<i class="fa-fw fa fa-' + iconClass + '"></i>'
    content: message
    color: color
    timeout: 4000
