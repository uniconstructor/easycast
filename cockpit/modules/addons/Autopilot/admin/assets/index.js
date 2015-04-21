(function($){

    App.module.controller("menu", function($scope, $rootScope, $http, $timeout){

        $scope.menues   = MENUES   || {};
        $scope.settings = SETTINGS || {};

        $scope.toggleActive = function(menu, id) {

            var active = !$scope.menues[menu][id].data.active,
                item   = {"_id":id,"active":active};

            $http.post(App.route("/api/autopilot/saveMenuItem"), {"item": item, "menu":menu}).success(function(data){

                $timeout(function(){
                    $scope.menues[menu][id].data.active = active;
                    $("#"+id).data("active", active);
                }, 0);

                App.notify(App.i18n.get("Menu item status updated!"), "success");

            }).error(App.module.callbacks.error.http);
        };


        $scope.remove = function(menu, id) {

            App.Ui.confirm(App.i18n.get("Are you sure?"), function() {

                $http.post(App.route("/api/autopilot/removeMenuItem"), {"item": id, "menu":menu}).success(function(data){

                    $("#"+id).find('li[id]').each(function(){
                      delete $scope.menues[menu][this.id];
                    }).remove().end().remove();

                    $timeout(function(){
                        delete $scope.menues[menu][id];
                    }, 0);

                    App.notify(App.i18n.get("Menu item removed!"), "success");

                }).error(App.module.callbacks.error.http);
            });
        };

        $scope.saveMenu = function(menu) {

            var structure = $('#menu-'+menu).data('nestable').serialize();

            $http.post(App.route("/api/autopilot/saveMenu"), {"structure": structure, "menu":menu}).success(function(data){

                App.notify(App.i18n.get("Menu updated!"), "success");

            }).error(App.module.callbacks.error.http);
        };


        $scope.$watch('settings', (function() {

            var init = false;

            return function() {

                if(!init) return (init = true);

                $http.post(App.route("/api/autopilot/updateSettings"), {"settings": angular.copy($scope.settings)}).success(function(data){

                    App.notify(App.i18n.get("Settings updated!"), "success");

                }).error(App.module.callbacks.error.http);
            };

        })(), true);

        // init menues
        $('.uk-nestable').each(function(){

            var list = UIkit.nestable(this, {maxDepth:3}).element.on('change.uk.nestable', function(e, item) {

                updateSlugs('main');
                $scope.saveMenu('main')
            });

            list.find('.js-menu-item').each(function(){
                var data = angular.copy($scope.menues[list.data('menu')][this.id].data);
                if(data.children)  delete data['children'];
                $(this).data(data);
            });
        });

       var $select = $('#select-homepage').on('change', function(){
            $timeout(function(){
                if (!$select.val()) return;
                $scope.settings.homepage = $select.val();
                $select.val('');
            });
       });

        function updateSlugs(menu) {

            $('#menu-'+menu).find('li[id]').each(function(){

                var slugPath = [];

                item = $(this);

                slugPath.push(item.data('slug'));

                item.parents('li[id]').each(function(){
                    slugPath.push($(this).data('slug'));
                });

                $scope.menues['main'][item.data('_id')].slug_path = '/'+slugPath.reverse().join('/');
            });
        }

    });

})(jQuery);
