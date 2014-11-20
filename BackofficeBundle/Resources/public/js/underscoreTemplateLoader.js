// Orchestra adaptation of the jQuery templateLoader plugin found at
// https://github.com/Gazler/Underscore-Template-Loader

(function() {
  var templateLoader = {
    templateVersion: "0.0.1",
    templates: {},
    loadRemoteTemplate: function(templateName, view) {
      if (!this.templates[templateName]) {
        var self = this;
        filename = appRouter.generateUrl('loadUndescroreTemplate', {templateId: templateName})
        jQuery.get(filename, function(data) {
          self.addTemplate(templateName, data);
          self.saveLocalTemplates();
          view.onTemplateLoaded(templateName, data);
        });
      }
      else {
        view.onTemplateLoaded(templateName, this.templates[templateName]);
      }
    },
    
    addTemplate: function(templateName, data) {
      this.templates[templateName] = data;
    },
    
    localStorageAvailable: function() {
      try {
        return 'localStorage' in window && window['localStorage'] !== null;
      } catch (e) {
        return false;
      }
    },
    
    saveLocalTemplates: function() {
      if (this.localStorageAvailable) {
        localStorage.setItem("templates", JSON.stringify(this.templates));
        localStorage.setItem("templateVersion", this.templateVersion);
      }
    },
    
    loadLocalTemplates: function() {
      if (this.localStorageAvailable) {
        var templateVersion = localStorage.getItem("templateVersion");
        if (templateVersion && templateVersion == this.templateVersion) {
          var templates = localStorage.getItem("templates");
          if (templates) {
            templates = JSON.parse(templates);
            for (var x in templates) {
              if (!this.templates[x]) {
                this.addTemplate(x, templates[x]);
              }
            }
          }
        }
        else {
          localStorage.removeItem("templates");
          localStorage.removeItem("templateVersion");
        }
      }
    }
  };
  
  templateLoader.loadLocalTemplates();
  window.templateLoader = templateLoader;
})();
