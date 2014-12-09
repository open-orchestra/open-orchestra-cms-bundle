showEntity = (url, language)->
  url = url + '?language=' + language if (typeof language != 'undefined')
  $.ajax
    type: "GET"
    url: url
    success: (response) ->
      if isLoginForm(response)
        redirectToLogin()
      else
        options = 
          title: 'test'
          listUrl: 'test'
        options = $.extend(options, {multiLanguage:
          language: language
          language_list: 'http://phporchestra.dev/app_dev.php/api/site/1'
          path: 'showEntityWithLanguage'
          path_option: {},
          url_entity: 'http://phporchestra.dev/app_dev.php/api/content/54857e7c78426dcd2f8b45d2'
        })
        view = new FullPageFormView(
          $.extend(options, {html: response})
        )
        appRouter.setCurrentMainView(view)
        return
  return
