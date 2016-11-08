import app from './OpenOrchestra/Application/Application'
import Configuration from './OpenOrchestra/Service/Configuration'

// variable config is define in layout.html.twig
let configuration = new Configuration(config);
app.setConfiguration(configuration);
$(() => {
    app.setRegions({
        'header': $('.header'),
        'left_column': $('#left_column'),
        'content': $('#central-column')
    });
    app.run();
});
