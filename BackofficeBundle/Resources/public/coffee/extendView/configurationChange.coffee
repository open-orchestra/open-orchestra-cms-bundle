extendView = extendView || {}
extendView['configurationChange'] = {
  events:
    'click .configuration-change': 'configurationChange'

  configurationChange: (event) ->
    target = $(event.currentTarget)
    url = target.data('url')
    window.location = url + '#' + Backbone.history.fragment
}
