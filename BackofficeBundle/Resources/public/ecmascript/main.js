import app           from './OpenOrchestra/Application/Application'
import Configuration from './OpenOrchestra/Service/Configuration'
import Context       from './OpenOrchestra/Service/Context'

// variable config and context is defined in layout.html.twig
app.setConfiguration(new Configuration(config));
app.setContext(new Context(context));

$(() => {
    app.setRegions({
        'header': $('.header-region'),
        'left_column': $('#left-column'),
        'content': $('#central-column'),
        'modal': $('.modal-region')
    });
    app.run();
});
