((router) ->
  router.route 'user/edit', 'editUserSelf', ->
    @initDisplayRouteChanges()
    pageTitle = $('#user-account').data('title')
    userUrl = $('#user-account').data('user-url')
    passwordUrl = $('#user-account').data('password-url')
    @editMyAccount pageTitle, userUrl, passwordUrl

  router.editMyAccount = (pageTitle, userUrl, passwordUrl)->
    drawBreadCrumb [pageTitle]
    tabViewClass = appConfigurationView.getConfiguration('user', 'showTab')
    tabView = new tabViewClass(
      'domContainer': $('#content')
    )
    setPageLogo('fa fa-fw fa-user')

    renderPanel = (tabView, tabViewType, panelUrl, tabId, tabIndex, isTabActive) ->
      $.ajax
        type: "GET"
        url: panelUrl
        success: (response) ->
          elementTabViewClass = appConfigurationView.getConfiguration(tabViewType, 'editEntityTab')
          panelView = new elementTabViewClass
            'response': response
            'listUrl': false
          tabView.addPanel $('[data-title]', panelView.$el).data('title'), tabId, panelView, isTabActive, tabIndex
          return

    renderPanel tabView, 'user_tab_selfForm', userUrl, 'user-form', 1, true
    renderPanel tabView, 'user_tab_selfPasswordForm', passwordUrl, 'password-form', 2, false

    return

) window.appRouter