liferoute.config(['$locationProvider', '$httpProvider', function($locationProvider, $httpProvider) {
    $locationProvider.hashPrefix('');
    //$locationProvider.html5Mode(true);
}]);

//routing
liferoute.config(function($stateProvider, $urlRouterProvider) {

    // For any unmatched url, redirect to /state1
    $urlRouterProvider.when("/", "/login");
    $urlRouterProvider.when("", "/login");
    $urlRouterProvider.otherwise('/404');

    //
    // Now set up the states
    $stateProvider.state('login', {
        url: "/login",
        containerClass: "form-bg",
        views: {
            'content': {
                templateUrl: 'views/login.html'
            }

        }

    }).state('forgot', {
        url: "/forgot",
        containerClass: "form-bg",
        views: {
            'content': {
                templateUrl: 'views/forgot.html'
            }

        }

    }).state('email-confirm', {
        url: "/email-confirm",
        containerClass: "form-bg",
        views: {
            'content': {
                templateUrl: 'views/email-confirm.html'
            }

        }

    }).state('dashboard-home', {
        url: "/dashboard-home",
        containerClass: "dashboard-container",
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-home.html'
            }

        }

    }).state('dashboard-notification', {
        url: "/dashboard-notification",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'All Notifications (22)'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-notification.html'
            }

        }

    }).state('dashboard-change-password', {
        url: "/dashboard-change-password",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Change Password'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-change-password.html'
            }

        }

    }).state('dashboard-messages', {
        url: "/dashboard-message",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Messages (10)'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-messages.html'
            }

        }

    }).state('dashboard-user-management', {
        url: "/dashboard-user-management",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'User Management'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-user-management.html'
            }

        }

    }).state('dashboard-user-dtl', {
        url: "/dashboard-user-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'User Profile Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-user-dtl.html'
            }

        }

    }).state('dashboard-route-listing', {
        url: "/dashboard-route-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Life Route Listing'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-route-listing.html'
            }

        }

    }).state('dashboard-route-dtl', {
        url: "/dashboard-route-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Life Route Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-route-dtl.html'
            }

        }

    }).state('dashboard-routestats-listing', {
        url: "/dashboard-routestats-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Life Route Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-routestats-listing.html'
            }

        }

    }).state('dashboard-routestats-dtl', {
        url: "/dashboard-routestats-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Life Route Stats Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-routestats-dtl.html'
            }

        }

    }).state('dashboard-news-listing', {
        url: "/dashboard-news-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'News Listing'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-news-listing.html'
            }

        }

    }).state('dashboard-news-dtl', {
        url: "/dashboard-news-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'News Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-news-dtl.html'
            }

        }

    }).state('dashboard-news-stats-listing', {
        url: "/dashboard-news-stats-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'News Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-news-stats-listing.html'
            }

        }

    }).state('dashboard-news-stats-dtl', {
        url: "/dashboard-news-stats-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'News Stats Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-news-stats-dtl.html'
            }

        }

    }).state('dashboard-event-listing', {
        url: "/dashboard-event-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Events Listing'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-event-listing.html'
            }

        }

    }).state('dashboard-event-dtl', {
        url: "/dashboard-event-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Event Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-event-dtl.html'
            }

        }

    }).state('dashboard-event-stats-listing', {
        url: "/dashboard-event-stats-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Events Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-event-stats-listing.html'
            }

        }

    }).state('dashboard-event-stats-dtl', {
        url: "/dashboard-event-stats-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Event Stats Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-event-stats-dtl.html'
            }

        }

    }).state('dashboard-business-listing', {
        url: "/dashboard-business-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Business Profiles'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-business-listing.html'
            }

        }

    }).state('dashboard-business-dtl', {
        url: "/dashboard-business-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Business Profile Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-business-dtl.html'
            }

        }

    }).state('dashboard-business-posts', {
        url: "/dashboard-business-posts",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Business Posts Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-business-posts.html'
            }

        }

    }).state('dashboard-business-posts-dtl', {
        url: "/dashboard-business-posts-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Business Posts Stats Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-business-posts-dtl.html'
            }

        }

    }).state('dashboard-adverts-listing', {
        url: "/dashboard-adverts-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Adverts For Approval'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-adverts-listing.html'
            }

        }

    }).state('dashboard-adverts-dtl', {
        url: "/dashboard-adverts-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Adverts For Approval'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-adverts-dtl.html'
            }

        }

    }).state('dashboard-adverts-stats-listing', {
        url: "/dashboard-adverts-stats-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Adverts Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-adverts-stats-listing.html'
            }

        }

    }).state('dashboard-adverts-stats-dtl', {
        url: "/dashboard-adverts-stats-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Adverts Stats'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-adverts-stats-dtl.html'
            }

        }

    }).state('dashboard-report-listing', {
        url: "/dashboard-report-listing",
        containerClass: "dashboard-container",
        contentClass: "padding-sm",
        data: {
            pageTitle: 'Users Issues & Reports'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-report-listing.html'
            }

        }

    }).state('dashboard-report-dtl', {
        url: "/dashboard-report-dtl",
        containerClass: "dashboard-container",
        data: {
            pageTitle: 'Report Details'
        },
        views: {
            'header': {
                templateUrl: 'template/header.html'
            },
            'menu': {
                templateUrl: 'template/menu.html'
            },
            'content': {
                templateUrl: 'views/dashboard-report-dtl.html'
            }

        }

    });

});

// Header Class
liferoute.run(function($state, $rootScope, $stateParams) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;
    $rootScope.$on('$stateChangeSuccess', function(event, toState, toParams, fromState, fromParams) {
        $rootScope.containerClass = toState.containerClass;
        $rootScope.contentClass = toState.contentClass;
        document.body.scrollTop = document.documentElement.scrollTop = 0;

    });

    $rootScope.$on("$stateChangeStart", function(event, toState, toParams, fromState, fromParams, current) {
        $rootScope.globalTitle = toState.data.pageTitle;
    });

});