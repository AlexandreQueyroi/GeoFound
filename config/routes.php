<?php
$routes = [
    '/' => ['controller' => 'HomeController', 'action' => 'index'],

    '/auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    '/auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    '/auth/register' => ['controller' => 'AuthController', 'action' => 'register'],
    '/auth/verify' => ['controller' => 'AuthController', 'action' => 'verify'],
    '/auth/resend-verification' => ['controller' => 'AuthController', 'action' => 'resendVerification'],

    '/user/profile' => ['controller' => 'UserController', 'action' => 'profile'],
    '/user/edit' => ['controller' => 'UserController', 'action' => 'edit'],
    '/user/inbox' => ['controller' => 'UserController', 'action' => 'inbox'],
    '/user/export/pdf' => ['controller' => 'UserController', 'action' => 'exportPdf'],
    '/user/export/json' => ['controller' => 'UserController', 'action' => 'exportJson'],

    '/friend' => ['controller' => 'FriendController', 'action' => 'index'],
    '/friend/add' => ['controller' => 'FriendController', 'action' => 'add'],
    '/friend/accept' => ['controller' => 'FriendController', 'action' => 'accept'],
    '/friend/refuse' => ['controller' => 'FriendController', 'action' => 'refuse'],
    '/friend/requests' => ['controller' => 'FriendController', 'action' => 'requests'],

    '/message/inbox' => ['controller' => 'MessageController', 'action' => 'inbox'],
    '/message/view' => ['controller' => 'MessageController', 'action' => 'view'],
    '/message/conversation' => ['controller' => 'MessageController', 'action' => 'conversation'],
    '/message/send' => ['controller' => 'MessageController', 'action' => 'send'],
    '/message/friends' => ['controller' => 'MessageController', 'action' => 'friends'],

    '/post' => ['controller' => 'PostController', 'action' => 'index'],
    '/post/view' => ['controller' => 'PostController', 'action' => 'view'],
    '/post/create' => ['controller' => 'PostController', 'action' => 'create'],

    '/reward' => ['controller' => 'RewardController', 'action' => 'index'],
    '/reward/unlock' => ['controller' => 'RewardController', 'action' => 'unlock'],

    '/avatar' => ['controller' => 'AvatarController', 'action' => 'index'],

    '/admin' => ['controller' => 'AdminController', 'action' => 'index'],
    '/admin/permissions' => ['controller' => 'AdminController', 'action' => 'permissions'],
    '/admin/rank' => ['controller' => 'AdminController', 'action' => 'rank'],
    '/admin/maintenance' => ['controller' => 'AdminController', 'action' => 'maintenance'],
    '/admin/users' => ['controller' => 'AdminController', 'action' => 'users'],
    '/admin/reports' => ['controller' => 'AdminController', 'action' => 'reports'],

    '/api' => ['controller' => 'ApiController', 'action' => 'index'],

    '/api/posts' => ['controller' => 'ApiController', 'action' => 'posts'],
    '/api/comment' => ['controller' => 'ApiController', 'action' => 'comment'],
    '/api/comments' => ['controller' => 'ApiController', 'action' => 'comments'],
    '/api/react' => ['controller' => 'ApiController', 'action' => 'react'],
    '/api/bookmark' => ['controller' => 'ApiController', 'action' => 'bookmark'],
    '/api/captcha' => ['controller' => 'ApiController', 'action' => 'captcha'],

    // Routes API d'administration
    '/api/admin/stats' => ['controller' => 'AdminController', 'action' => 'apiStats'],
    '/api/admin/activity' => ['controller' => 'AdminController', 'action' => 'apiActivity'],
    '/api/admin/permissions' => ['controller' => 'AdminController', 'action' => 'apiPermissions'],
    '/api/admin/ranks' => ['controller' => 'AdminController', 'action' => 'apiRanks'],
    '/api/admin/rank-permissions' => ['controller' => 'AdminController', 'action' => 'apiRankPermissions'],
    '/api/admin/user-permissions' => ['controller' => 'AdminController', 'action' => 'apiUserPermissions'],
    '/api/admin/maintenance' => ['controller' => 'AdminController', 'action' => 'apiMaintenance'],
    '/api/admin/page-permissions' => ['controller' => 'AdminController', 'action' => 'apiPagePermissions'],
    '/api/admin/users' => ['controller' => 'AdminController', 'action' => 'apiUsers'],
    '/api/admin/users/([0-9]+)' => ['controller' => 'AdminController', 'action' => 'apiUser'],
    '/api/admin/users/([0-9]+)/toggle-status' => ['controller' => 'AdminController', 'action' => 'apiToggleUserStatus'],

    '/403' => ['controller' => 'ErrorController', 'action' => 'error403'],
    '/404' => ['controller' => 'ErrorController', 'action' => 'error404'],
    '/500' => ['controller' => 'ErrorController', 'action' => 'error500'],

    '/me' => ['controller' => 'UserController', 'action' => 'profile'],
    '/me/edit' => ['controller' => 'UserController', 'action' => 'edit'],
    '/me/inbox' => ['controller' => 'UserController', 'action' => 'inbox'],

    '/admin/reports/view' => ['controller' => 'AdminController', 'action' => 'viewReport'],
    '/admin/reports/action' => ['controller' => 'AdminController', 'action' => 'handleReportAction'],
    '/api/report' => ['controller' => 'ApiController', 'action' => 'createReport'],
]; 