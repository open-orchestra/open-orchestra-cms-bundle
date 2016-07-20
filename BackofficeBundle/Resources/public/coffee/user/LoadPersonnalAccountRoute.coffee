((router) ->
  router.route 'user/edit', 'editUserSelf', ->
    @initDisplayRouteChanges()
    editPersonnalAccount $('#user-account').data('user-url'), $('#user-account').data('password-url')
) window.appRouter

editPersonnalAccount = (userUrl, passwordUrl)->
  tabViewClass = appConfigurationView.getConfiguration('user', 'showTab')
  @tabView = new tabViewClass(
    'domContainer': $('#content')
  )

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
