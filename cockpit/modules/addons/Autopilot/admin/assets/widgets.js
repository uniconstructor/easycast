(function($){

    App.module.controller("widgets", function($scope, $rootScope, $http, $timeout){

        $scope.widgets   = WIDGETS || [];
        $scope.positions = POSITIONS || [];
        $scope.activepos = '-all';
        $scope.filter    = "";

        $scope.remove = function(index, widget){

            App.Ui.confirm(App.i18n.get("Are you sure?"), function() {

                $http.post(App.route("/api/autopilot/removeWidget"), { "widget": angular.copy(widget) }, {responseType:"json"}).success(function(data){

                    $timeout(function(){
                        $scope.widgets.splice(index, 1);
                        App.notify(App.i18n.get("Widget removed"), "success");
                    }, 0);
                }).error(App.module.callbacks.error.http);
            });
        };

        $scope.updateWidget = function(widget, property, value) {

            widget[property] = value;

            $http.post(App.route("/api/autopilot/saveWidget"), { "widget": angular.copy(widget),  }, {responseType:"json"}).success(function(data){

                $timeout(function(){
                    App.notify(App.i18n.get("Widget updated"), "success");
                }, 0);
            }).error(App.module.callbacks.error.http);
        };

        $scope.duplicate = function(widgetId){

            $http.post(App.route("/api/autopilot/duplicateWidget"), {"widgetId": widgetId }, {responseType:"json"}).success(function(widget){

                $timeout(function(){
                    $scope.widgets.push(widget);
                    App.notify(App.i18n.get("Widget duplicated"), "success");
                }, 0);
            }).error(App.module.callbacks.error.http);
        };

        $scope.matchTitle = function(title) {
            return title && (title.indexOf($scope.filter) !== -1);
        };

        $scope.inPosition = function(position) {
            return ($scope.activepos=='-all' || $scope.activepos==position);
        };


        var $list = $('#js-widgets').on("change.uk.sortable", function(e, sortable, ele){

            if(!ele) return;

            var ele      = $(ele),
                widget   = ele.scope().widget,
                position = widget.position,
                widgets  = [],
                order    = 0;

            $scope.widgets.splice(ele.index(), 0, $scope.widgets.splice($scope.widgets.indexOf(widget), 1)[0]);

            $scope.widgets.forEach(function(widget) {
                if (widget.position==position) {
                    widgets.push(widget._id);
                    widget.order = order++;
                }
            });

            $http.post(App.route("/api/autopilot/updateWidgetOrder"), {"widgets": widgets }, {responseType:"json"}).success(function(widget){

                $timeout(function(){
                    App.notify(App.i18n.get("Widgets order updated"), "success");
                });
            }).error(App.module.callbacks.error.http);
        });
    });

})(jQuery);
