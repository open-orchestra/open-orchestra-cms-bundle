module.exports = {
  src: [
    //--[ ROUTING ]--//
    'web/js/fos_js_routes.js',

    //--[ TRANSLATION ]--//
    'web/js/translations/*/*.js',

    //--[ LIB ORCHESTRA ]--//
    'web/built/**/Lib/*.js',

    //--[ TEMPLATE UNDERSCORE ]--//
    'web/built/template/templates.js',

    //--[ NAVIGATION CONFIG ]--//
    'web/built/navigation/navigation.js',

    //--[ APPLICATION ]--//
    'web/built/oo_application.js'
  ],
  dest: 'web/built/orchestra.js'
};
