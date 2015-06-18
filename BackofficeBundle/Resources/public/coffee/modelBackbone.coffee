SiteModel = Backbone.Model.extend({})

BlockModel = Backbone.Model.extend({})
BlockCollection = Backbone.Collection.extend(model: BlockModel)

Area = Backbone.Model.extend(
  blocks: BlockCollection
  areas: AreaCollection
)
AreaCollection = Backbone.Collection.extend(model: BlockModel)

NodeModel = Backbone.Model.extend(
  areas: AreaCollection,
  blocks: BlockCollection
)
NodeCollection = Backbone.Collection.extend(model: NodeModel)

TemplateModel = Backbone.Model.extend(
  areas: AreaCollection
)

TableviewModel = Backbone.Model.extend({})

VersionModel = Backbone.Model.extend({})

GalleryModel = Backbone.Model.extend({})
GalleryCollection = Backbone.Collection.extend(model: GalleryModel)
GalleryElement = Backbone.Model.extend(
  sites: GalleryCollection
)
