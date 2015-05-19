userPanelViewParam = []

userPanelView = OrchestraView.extend(
  className: 'superbox-show'
  el: '#content'

  events:
    'click a#profile_action_button': 'setupProfile'
    'click a#password_action_button': 'setupPassword'

  initialize: (options) ->
    @user = options.user
    @listUrl = appRouter.generateUrl('listEntities', entityType: "user")
    @loadTemplates [
      'OpenOrchestraUserAdminBundle:BackOffice:Underscore/userPanelView'
    ]

  render: ->
    currentView = this
    $(@el).html @renderTemplate('OpenOrchestraUserAdminBundle:BackOffice:Underscore/userPanelView',
      user: @user
      listUrl: @listUrl
    )
    $('.js-widget-title', @$el).text @user.get('name')
    @setupProfileForm()
    @setupPasswordForm()
    displayLoader('#alternative-loader')

  setupProfileForm: ->
    currentView = this
    displayLoader('#selector-loader')
    $.ajax
      url: @user.get('links')._self_form
      method: 'GET'
      success: (response) ->
        $('#selector-loader-container').hide()
        $('.user_profile_form', currentView.$el).html response
        currentView.addEventOnProfileForm()

  setupPasswordForm: ->
    currentView = this
    displayLoader('.user_password_form')
    $.ajax
      url: @user.get('links')._self_reset_password
      method: 'GET'
      success: (response) ->
        $('.user_password_form', currentView.$el).html response
        currentView.addEventOnPasswordForm()

  addEventOnProfileForm: ->
    currentView = this
    $(".user_profile_form form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            $('.user_profile_form', currentView.$el).html response
            currentView.addEventOnProfileForm()
          400: ->
            $('.user_profile_form', currentView.$el).html response
            currentView.addEventOnProfileForm()
    return

  addEventOnPasswordForm: ->
    currentView = this
    $(".user_password_form form", @$el).on "submit", (e) ->
      e.preventDefault() # prevent native submit
      $(this).ajaxSubmit
        statusCode:
          200: (response) ->
            $('.user_password_form', currentView.$el).html response
            currentView.addEventOnPasswordForm()
          400: (response) ->
            $('.user_password_form', currentView.$el).html response
            currentView.addEventOnPasswordForm()
    return
)
