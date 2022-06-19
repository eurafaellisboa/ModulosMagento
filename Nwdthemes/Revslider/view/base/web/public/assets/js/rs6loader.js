define('rs6loader', [], function() {
    return function(config) {
        var scripts = config.scripts;
        scripts.push('rs6');
        require(scripts, function() {});
    }
});