<?php
$routes = [
    '/' => ['controller' => 'HomeController', 'action' => 'index'],

    '/auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    '/auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    '/auth/register' => ['controller' => 'AuthController', 'action' => 'register'],

    '/user/profile' => ['controller' => 'UserController', 'action' => 'profile'],
    '/user/edit' => ['controller' => 'UserController', 'action' => 'edit'],
    '/user/inbox' => ['controller' => 'UserController', 'action' => 'inbox'],

    '/friend' => ['controller' => 'FriendController', 'action' => 'index'],
    '/friend/add' => ['controller' => 'FriendController', 'action' => 'add'],
    '/friend/accept' => ['controller' => 'FriendController', 'action' => 'accept'],

    '/message/inbox' => ['controller' => 'MessageController', 'action' => 'inbox'],
    '/message/view' => ['controller' => 'MessageController', 'action' => 'view'],

    '/post' => ['controller' => 'PostController', 'action' => 'index'],
    '/post/view' => ['controller' => 'PostController', 'action' => 'view'],

    '/reward' => ['controller' => 'RewardController', 'action' => 'index'],
    '/reward/unlock' => ['controller' => 'RewardController', 'action' => 'unlock'],

    '/avatar' => ['controller' => 'AvatarController', 'action' => 'index'],

    '/admin' => ['controller' => 'AdminController', 'action' => 'index'],
    '/admin/permissions' => ['controller' => 'AdminController', 'action' => 'permissions'],
    '/admin/maintenance' => ['controller' => 'AdminController', 'action' => 'maintenance'],

    '/api' => ['controller' => 'ApiController', 'action' => 'index'],

    '/api/posts' => ['controller' => 'ApiController', 'action' => 'posts'],
    '/api/comment' => ['controller' => 'ApiController', 'action' => 'comment'],
    '/api/comments' => ['controller' => 'ApiController', 'action' => 'comments'],
    '/api/react' => ['controller' => 'ApiController', 'action' => 'react'],
    '/api/bookmark' => ['controller' => 'ApiController', 'action' => 'bookmark'],

    '/403' => ['controller' => 'ErrorController', 'action' => 'error403'],
    '/404' => ['controller' => 'ErrorController', 'action' => 'error404'],
    '/500' => ['controller' => 'ErrorController', 'action' => 'error500'],
]; 