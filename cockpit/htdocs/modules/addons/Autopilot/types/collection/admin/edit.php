{%

    $collections = $app->db->find('common/collections')->toArray();
%}

<div class="uk-grid" data-uk-grid-margin ng-controller="collectionMenuItem">
    <div class="uk-width-2-3">
        <div class="uk-form-row">
            <label><span class="uk-badge app-badge">@lang('Collection')</span></label>
            <div class="uk-margin-small-top">
                <select class="uk-form-large uk-width-1-1" ng-model="data.collectionId">
                    @foreach($collections as $collection)
                    <option value="{{ $collection['_id'] }}">{{ $collection['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uk-margin-small-top uk-text-small" ng-show="data.collectionId">
                <a ng-click="manageCollection(data.collectionId)"><i class="uk-icon-list"></i> Manage items</a>
            </div>
        </div>
    </div>
    <div class="uk-width-1-3">
        <div class="uk-form-row">
            <label><span class="uk-text-small">@lang('Items on list view')</span></label>
            <div class="uk-margin-small-top">
                <input type="text" class="uk-form-large uk-width-1-1" ng-model="data.listItemsLimit" placeholder="10">
            </div>
        </div>
    </div>
</div>

<script>

    (function($){

        App.module.controller("collectionMenuItem", function($scope, $rootScope, $http, $timeout){

            $scope.manageCollection = function(collectionId) {

                var route = 'collections/entries/'+collectionId;

                App.viewpopup(route);
            };

        });

    })(jQuery);
</script>