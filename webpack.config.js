const Encore = require('@symfony/webpack-encore');

Encore
    // répertoire où seront placés les fichiers compilés
    .setOutputPath('public/build/')

    // URL publique utilisée par le serveur web pour accéder aux fichiers
    .setPublicPath('/build')

    // votre fichier principal d'entrée
    .addEntry('app', './assets/app.js')

    // active le traitement des fichiers SCSS
    .enableSassLoader()

    // nettoyage automatique du répertoire output avant chaque compilation
    .cleanupOutputBeforeBuild()

    // active l'encapsulation des fichiers pour éviter les conflits
    .enableSourceMaps(!Encore.isProduction())

    // active la compression des fichiers pour la production
    .enableVersioning( Encore.isProduction() )
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();