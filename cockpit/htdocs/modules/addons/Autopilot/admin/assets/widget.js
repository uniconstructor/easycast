(function($){

    App.module.controller("widget", function($scope, $rootScope, $http, $timeout){

        $scope.widget    = WIDGET;
        $scope.positions = POSITIONS || {};

        $scope.save = function() {

            var widget = angular.copy($scope.widget);

            $http.post(App.route("/api/autopilot/saveWidget"), {"widget": widget}).success(function(response){

                if (response && Object.keys(response).length) {

                    $scope.widget._id = response._id;

                    App.notify(App.i18n.get("Widget saved!"), "success");
                }

            }).error(App.module.callbacks.error.http);
        };


        $('html').on('click', '.js-link-acl', function() {

            var checked = $(this).prop('checked'),
                linkId  = this.id;

            $timeout(function(){

                if (checked) {
                    $scope.widget.ac_links.push(linkId);
                } else {
                    var index = $scope.widget.ac_links.indexOf(linkId);
                    $scope.widget.ac_links.splice(index, 1);
                }
            });


        }).find('.js-link-acl').each(function(){

            if (!$scope.widget.ac_links.length) {
                return;
            }

            if ($scope.widget.ac_links.indexOf(this.id) > -1) {
               $(this).prop('checked', true);
            }
        });

        // bind clobal command + save
        Mousetrap.bindGlobal(['command+s', 'ctrl+s'], function(e) {
            e.preventDefault();
            $scope.save();
            return false;
        });

    });

})(jQuery);