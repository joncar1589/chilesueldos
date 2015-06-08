var angular_grocery_crud = angular.module('angular_grocery_crud',['ui.bootstrap']).filter('to_trusted', ['$sce', function($sce){
    return function(text) {
        return $sce.trustAsHtml(text);
    };
}]);