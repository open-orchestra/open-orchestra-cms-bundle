// Orchestra adaptation of the jQuery templateLoader plugin found at
// https://github.com/Gazler/Underscore-Template-Loader

(function() {
  var templateLoader = {
    templates: {},
    loadRemoteTemplate: function(templateName, language, view) {
      if (!this.templates[language]) {
        this.addLanguage(language);
      }
      if (!this.templates[language][templateName]) {
        var self = this;
        filename = appRouter.generateUrl('loadUnderscoreTemplate', {'language': language, 'templateId': templateName})
        jQuery.get(filename, function(tpl) {
          self.addTemplate(templateName, language, tpl);
          self.saveLocalTemplates();
          view.onTemplateLoaded(templateName, tpl);
        });
      }
      else {
        view.onTemplateLoaded(templateName, this.templates[language][templateName]);
      }
    },
    
    addTemplate: function(templateName, language, tpl) {
      if (!this.templates[language]) {
        this.addLanguage(language);
      }
      this.templates[language][templateName] = tpl;
    },
    
    addLanguage: function(language) {
     this.templates[language] = {};
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
    
    loadLocalTemplates: function(options) {
      this.templateVersion = options.templateVersion;
      if (this.localStorageAvailable) {
        var templateVersion = localStorage.getItem("templateVersion");
        if (templateVersion && templateVersion == this.templateVersion) {
          var templates = localStorage.getItem("templates");
          if (templates) {
            templates = JSON.parse(templates);
            for (var language in templates) {
              if (!this.templates[language]) {
                this.addLanguage(language);
              }
              for (var templateName in templates[language]) {
                if (!this.templates[language][templateName]) {
                  this.addTemplate(templateName, language, templates[language][templateName]);
                }
              }
            }
          }
        } else {
          localStorage.removeItem("templates");
          localStorage.removeItem("templateVersion");
        }
      }
    }
  };
  templateLoader.loadLocalTemplates({templateVersion: $('#assets-version').html()});
  window.templateLoader = templateLoader;
})();
