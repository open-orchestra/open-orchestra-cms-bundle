import Application   from './OpenOrchestra/Application/Application'
import Configuration from './OpenOrchestra/Service/Configuration'
import Context       from './OpenOrchestra/Service/Context'

// variable config and context is defined in layout.html.twig
Application.setConfiguration(new Configuration(config));
Application.setContext(new Context(context));

$(() => {
    Application.setRegions({
        'header': $('.header-region'),
        'left_column': $('#left-column'),
        'content': $('.content-region'),
        'breadcrumb': $('.breadcrumb-region'),
        'modal': $('.modal-region')
    });
    Application.run();
});
