chai = require ("chai")
chai.should()

{Block} = require ("../../../../Resources/public/coffee/block")

describe "block Instance", ->
  block = null
  blockResponse =
    component: "Sample"
    method: "generate"
    attributes:
      author: ""
      title: "Accueil"
      news: "Bienvenue"
    ui_model:
      html: "<span>datetime : 1407753067 </span>"
      label: "Sample"
  it 'should contain a blockResponse object', ->
    block = new Block blockResponse
    block.blockResponse.should.equal blockResponse
  it 'shound render the label in the title', ->
    block.renderTitle().should.contain '<span class="title">Sample</span>'
  it 'should render the action button', ->
    block.renderActionButton().should.contain '<span class="action"><i class="fa fa-cog"></i></span>'
  it 'should render the preview', ->
    block.renderPreview().should.contain '<span class="preview"><div><span>datetime : 1407753067 </span><br>Title : Accueil<br>Author : <br>News : Bienvenue<br></div></span>'
  it 'should print html with the component class', ->
    block.printHtml().should.contain '<div class="generate">'
    block.printHtml().should.contain '<span class="title">'
    block.printHtml().should.contain '<span class="action">'
    block.printHtml().should.contain '<span class="preview">'
    block.printHtml().should.contain '</div>'