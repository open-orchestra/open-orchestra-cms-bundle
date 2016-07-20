((router) ->
  router.route 'user/edit', 'editUserSelf', ->
    @initDisplayRouteChanges()
    pageTitle = $('#user-account').data('title')
    userUrl = $('#user-account').data('user-url')
    passwordUrl = $('#user-account').data('password-url')
    editPersonnalAccount pageTitle, userUrl, passwordUrl
) window.appRouter

editPersonnalAccount = (pageTitle, userUrl, passwordUrl)->
  drawBreadCrumb [pageTitle]
  tabViewClass = appConfigurationView.getConfiguration('user', 'showTab')
  @tabView = new tabViewClass(
    'domContainer': $('#content')
  )
  setPageLogo('fa fa-fw fa-user')

  renderPanel = (tabViewType, panelUrl, tabId, tabIndex, isTabActive) ->
    $.ajax
      type: "GET"
      url: panelUrl
      success: (response) ->
        elementTabViewClass = appConfigurationView.getConfiguration(tabViewType, 'editEntityTab')
        panelView = new elementTabViewClass
          'response': response
          'listUrl': false
        window.tabView.addPanel $('[data-title]', panelView.$el).data('title'), tabId, panelView, isTabActive, tabIndex
        return

  renderPanel 'user_tab_selfForm', userUrl, 'user-form', 1, true
  renderPanel 'user_tab_selfPasswordForm', passwordUrl, 'password-form', 2, false

  return
