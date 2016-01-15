extendView = extendView || {}
extendView['concurrency'] = {
  addConcurrency: ->
    if typeof @currentRankView == 'undefined'
      @oldRender = @render
      @currentRankView = 0
    else
      @currentRankView = @currentRankView + 1
    @render = () ->
        if @currentRankView == @::currentRankView
          @oldRender.apply this, arguments
        return
    return
}
