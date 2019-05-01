 var liferoute = angular.module('myApp', ['ui.bootstrap', 'vjs.video','fancyboxplus', 'ui.router', 'ui.router.state.events', 'angular-owl-carousel-2', 'ngTagsInput', 'ngAnimate', 'angular-nicescroll']);

 //modal
 liferoute.controller('ModalCtrl', function($scope, $uibModal) {

     var $ctrl = this;
     $scope.animationsEnabled = true;


     //reply-msg
     $scope.reply = function() {
         $uibModal.open({
             templateUrl: 'template/message-reply.html',
             controller: 'replyCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };

     //user-search

     $scope.usersearch = function() {
         $uibModal.open({
             templateUrl: 'template/user-search.html',
             controller: 'usearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     $scope.userdelete = function() {
         $uibModal.open({
             templateUrl: 'template/user-delete.html',
             controller: 'userdeleteCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     $scope.userblock = function() {
         $uibModal.open({
             templateUrl: 'template/user-block.html',
             controller: 'userblockCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     //life-listing-opt

     $scope.routedelete = function() {
         $uibModal.open({
             templateUrl: 'template/route-delete.html',
             controller: 'routedeleteCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     $scope.routeblock = function() {
         $uibModal.open({
             templateUrl: 'template/route-block.html',
             controller: 'routeblockCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };

     //news-listing-opt


     $scope.newsdelete = function() {
         $uibModal.open({
             templateUrl: 'template/news-delete.html',
             controller: 'newsdeleteCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     $scope.newsblock = function() {
         $uibModal.open({
             templateUrl: 'template/news-block.html',
             controller: 'newsblockCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     //event-listing-opt


     $scope.eventdelete = function() {
         $uibModal.open({
             templateUrl: 'template/event-delete.html',
             controller: 'eventdeleteCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     $scope.eventblock = function() {
         $uibModal.open({
             templateUrl: 'template/event-block.html',
             controller: 'eventblockCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     //business-listing-opt


     $scope.businessdelete = function() {
         $uibModal.open({
             templateUrl: 'template/business-delete.html',
             controller: 'businessdeleteCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     $scope.businessblock = function() {
         $uibModal.open({
             templateUrl: 'template/business-block.html',
             controller: 'businessblockCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };


     // reject

     $scope.reject = function() {
         $uibModal.open({
             templateUrl: 'template/reject.html',
             controller: 'rejectCtrl',
             windowClass: 'app-modal app-modal-sm',
             size: 'md',
             animation: true,
         });

     };

     //route-search
     $scope.routesearch = function() {
         $uibModal.open({
             templateUrl: 'template/route-search.html',
             controller: 'routesearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     //stats-search

     $scope.statssearch = function() {
         $uibModal.open({
             templateUrl: 'template/stats-search.html',
             controller: 'statssearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     //video-links
     $scope.video = function() {
         $uibModal.open({
             templateUrl: 'template/video-links.html',
             controller: 'videoCtrl',
             windowClass: 'video-modal',
             size: 'lg',
             animation: true,
         });

     };

     //total-images
     $scope.images = function() {
         $uibModal.open({
             templateUrl: 'template/images.html',
             controller: 'imagesCtrl',
             windowClass: 'images-modal',
             size: 'lg',
             animation: true,
         });

     };

     //news-search
     $scope.newssearch = function() {
         $uibModal.open({
             templateUrl: 'template/news-search.html',
             controller: 'newssearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     $scope.nstatssearch = function() {
         $uibModal.open({
             templateUrl: 'template/news-stats-search.html',
             controller: 'nstatsCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     //event-search

     $scope.eventsearch = function() {
         $uibModal.open({
             templateUrl: 'template/event-search.html',
             controller: 'eventsearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };


     $scope.estatssearch = function() {
         $uibModal.open({
             templateUrl: 'template/event-stats-search.html',
             controller: 'estatsCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     //business-search

     $scope.businesssearch = function() {
         $uibModal.open({
             templateUrl: 'template/business-search.html',
             controller: 'bearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };


     $scope.bstatssearch = function() {
         $uibModal.open({
             templateUrl: 'template/business-stats-search.html',
             controller: 'bstatsCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };


     //advert-search


     $scope.advertsearch = function() {
         $uibModal.open({
             templateUrl: 'template/advert-search.html',
             controller: 'advertearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     $scope.astatssearch = function() {
         $uibModal.open({
             templateUrl: 'template/advert-stats-search.html',
             controller: 'astatsearchCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };

     //report search


     $scope.reportsearch = function() {
         $uibModal.open({
             templateUrl: 'template/report-search.html',
             controller: 'reportCtrl',
             windowClass: 'search-modal',
             size: 'lg',
             animation: true,
         });

     };


     //work-history

     $scope.workhistory = function() {
         $uibModal.open({
             templateUrl: 'template/workhistory.html',
             controller: 'historyCtrl',
             windowClass: 'app-modal',
             size: 'lg',
             animation: true,
         });

     };



 });


 liferoute.controller('replyCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 liferoute.controller('rejectCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });



 liferoute.controller('reportCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.report-search-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);


 });



 liferoute.controller('usearchCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });

 liferoute.controller('routesearchCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });

 liferoute.controller('astatsearchCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.advert-search-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);

 });



 liferoute.controller('advertearchCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.advert-search-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);

 });


 liferoute.controller('bstatsCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.business-search-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);
 });



 liferoute.controller('bearchCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.business-search-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);

 });




 liferoute.controller('eventsearchCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });



 liferoute.controller('estatsCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.event-stats-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);



 });




 liferoute.controller('newssearchCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });

 liferoute.controller('nstatsCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.news-stats-modal .field-group').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);



 });

 liferoute.controller('imagesCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.images-list').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);
 });



 liferoute.controller('videoCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.video-links-list').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);
 });



 liferoute.controller('statssearchCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
     $timeout(function() {

         document.querySelector('.s-scroll').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);
 });

 liferoute.controller('userdeleteCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });
 liferoute.controller('userblockCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });

 liferoute.controller('historyCtrl', function($scope, $uibModalInstance, $timeout) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };

     $timeout(function() {

         document.querySelector('.work-history-list').addEventListener('mouseover', function() {
             //angular.element(document.querySelector('.work-history-list')).getNiceScroll().resize();
             angular.element(document.querySelector("div[id^='ascrail']")).show();
         });
     }, 5);
 });


 liferoute.controller('routedeleteCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 liferoute.controller('routeblockCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });



 liferoute.controller('newsdeleteCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 liferoute.controller('newsblockCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });

 liferoute.controller('eventdeleteCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 liferoute.controller('eventblockCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


  liferoute.controller('businessdeleteCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 liferoute.controller('businessblockCtrl', function($scope, $uibModalInstance) {
     $scope.cancel = function() {
         $uibModalInstance.dismiss('cancel');
     };
 });


 //news-dtl
 liferoute.controller('newsCtrl', function($scope, $timeout) {
     var owlAPi;
     $scope.items = [1, 2, 3, 4];
     $scope.properties = {
         nav: true,
         navContainer: '#sliderNav',
         navText: ['<span class="prev"><i class="icon-arrow-left"></i></span>', '<span class="next"><i class="icon-arrow-right"></i></span>'],
         loop: true,
         margin: 0,
         dots: false,
         items: 1,
         autoplay: true
     };

     $scope.ready = function($api) {
         owlAPi = $api;

     };
 });


 //page-dtl-controller
 liferoute.controller('pagedtlCtrl', function($scope, $timeout, $timeout) {
     var myEl = angular.element('.page-dtl-overlay');
     var myE2 = angular.element('.page-dtl-overlay-container');
     $scope.mainpopup = function() {
         myEl.addClass('open');
         myE2.addClass('show');
         angular.element('body').addClass('scroll-hide');

     }
     $scope.mainpopupclose = function() {
         myEl.removeClass('open');
         myE2.removeClass('show');
         angular.element('body').removeClass('scroll-hide');
     }

     var owlAPi;
     $scope.items = [1, 2];
     $scope.properties = {
         nav: true,
         navContainer: '#customnav',
         navText: ['<span class="prev"><i class="icon-arrow-left"></i></span>', '<span class="next"><i class="icon-arrow-right"></i></span>'],
         loop: true,
         margin: 0,
         dots: false,
         items: 1,
         autoplay: true
     };

     $scope.ready = function($api) {
         owlAPi = $api;

     };
 });