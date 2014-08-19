Block = Backbone.Model.extend({})
BlockCollection = Backbone.Collection.extend(model: Block)
Area = Backbone.Model.extend(
  blocks: BlockCollection
  areas: AreaCollection
)
AreaCollection = Backbone.Collection.extend(model: Block)
Node = Backbone.Model.extend(
  areas: AreaCollection,
  blocks: BlockCollection
)
Template = Backbone.Model.extend(
  areas: AreaCollection,
  blocks: BlockCollection
)
