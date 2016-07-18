SiteModel = Backbone.Model.extend({})

TableviewModel = Backbone.Model.extend({})

VersionModel = Backbone.Model.extend({})

GalleryModel = Backbone.Model.extend({})
GalleryCollection = Backbone.Collection.extend(model: GalleryModel)
GalleryElement = Backbone.Model.extend(
  sites: GalleryCollection
)
