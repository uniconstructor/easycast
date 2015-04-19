(function($){

    App.module.controller("menuItem", function($scope, $rootScope, $http, $timeout){

        var menu = MENU || 'main';

        $scope.item = MENUITEM || {
            "title"  : "",
            "type"   : "",
            "slug"   : "",
            "active" : true
        };

        $scope.data = MENUITEMDATA || {};

        $scope.save = function() {

            var item = angular.copy($scope.item),
                data = angular.copy($scope.data);

            $http.post(App.route("/api/autopilot/saveMenuItem"), {"item": item, "menu":menu}).success(function(response){

                if (response && Object.keys(response).length) {

                    $scope.item._id = response._id;
                    item._id = response._id;

                    $http.post(App.route("/api/autopilot/saveMenuItemData"), {"item": response._id, "menu":menu, "data":data}).success(function(response){

                        $timeout(function(){
                           App.notify(App.i18n.get("Menu item saved!"), "success");
                        });
                    });
                }

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
