var app = angular.module('bar', ['oitozero.ngSweetAlert','angucomplete-alt','nsPopover'], function($interpolateProvider){

	$interpolateProvider.startSymbol('[['); $interpolateProvider.endSymbol(']]');
})
    .constant('API_URL', 'http://192.168.0.112/bar/public/api/v1/');



    app.filter('comma2decimal', [
function() { // should be altered to suit your needs
    return function(input) {
    var ret=(input)?input.toString().trim().replace(",","."):null;
        return ret;
    };
}]);
