(function($){

    App.module.controller("settings", function($scope, $rootScope, $http, $timeout){

        $scope.settings = SETTINGS || {};

        $scope.save = function(menu) {

            $http.post(App.route("/api/autopilot/updateSettings"), {"settings": angular.copy($scope.settings)}).success(function(data){

                App.notify(App.i18n.get("Settings updated!"), "success");

            }).error(App.module.callbacks.error.http);
        };

        // bind clobal command + save
        Mousetrap.bindGlobal(['command+s', 'ctrl+s'], function(e) {
            e.preventDefault();
            $scope.save();
            return false;
        });
    });

})(jQuery);
