<?php

/* --------------------------------------------------------------------

  Chevereto
  https://chevereto.com/

  @author	Rodolfo Berrios A. <http://rodolfoberrios.com/>
            <inbox@rodolfoberrios.com>

  Copyright (C) Rodolfo Berrios A. All rights reserved.

  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  https://chevereto.com/license

  --------------------------------------------------------------------- */

$route = function ($handler) {
    $logged_user = CHV\Login::getUser();
    if ($handler::getCond('search_enabled') == false) {
        return $handler->issue404();
    }
    if ($_POST !== [] and !$handler::checkAuthToken($_REQUEST['auth_token'] ?? null)) {
        $handler->template = 'request-denied';
        return;
    }
    if ($handler->isRequestLevel(4)) {
        return $handler->issue404();
    } // Allow only 3 levels
    if (empty($handler->request[0])) {
        return $handler->issue404();
    }
    CHV\User::statusRedirect($logged_user['status'] ?? null);
    if (!in_array($handler->request[0], ['images', 'albums', 'users'])) {
        return $handler->issue404();
    }
    $search = new CHV\Search;
    $search->q = $_REQUEST['q'] ?? null;
    $search->type = $handler->request[0];
    $search->request = $_REQUEST;
    $search->requester = CHV\Login::getUser();
    $search->build();
    if (!G\check_value($search->q)) {
        return G\redirect();
    }
    $safe_html_search = G\safe_html($search->display);
    try {
        $list_params = CHV\Listing::getParams(); // Use CHV magic params
        $handler::setVar('list_params', $list_params);
        $list = new CHV\Listing;
        $list->setType($search->type);
        if(isset($list_params['reverse'])) {
            $list->setReverse($list_params['reverse']);
        }
        if(isset($list_params['seek'])) {
            $list->setSeek($list_params['seek']);
        }
        $list->setOffset($list_params['offset']);
        $list->setLimit($list_params['limit']); // how many results?
        $list->setItemsPerPage($list_params['items_per_page']); // must
        $list->setSortType($list_params['sort'][0]); // date | size | views
        $list->setSortOrder($list_params['sort'][1]); // asc | desc
        $list->setWhere($search->wheres);
        $list->setRequester(CHV\Login::getUser());
        foreach ($search->binds as $k => $v) {
            $list->bind($v['param'], $v['value']);
        }
        $list->output_tpl = $search->type;
        $list->exec();
    } catch (Exception $e) {
    }
    $tabs = CHV\Listing::getTabs([
        'listing'	=> 'search',
        'basename'	=> 'search',
        'params'	=> ['q' => $safe_html_search['q'], 'page' => '1'],
        'params_remove_keys' => ['sort'],
    ]);
    foreach ($tabs as $k => &$v) {
        $v['current'] = $v['type'] == $search->type;
    }
    switch ($search->type) {
        case 'images':
            $meta_description = _s('Image search results for %s');
        break;
        case 'albums':
            $meta_description = _s('Album search results for %s');
        break;
        case 'users':
            $meta_description = _s('User search results for %s');
        break;
    }
    $handler::setVar('pre_doctitle', _s('Search'));
    $handler::setVar('meta_description', sprintf($meta_description, $safe_html_search['q']));
    $handler::setVar('search', $search->display);
    $handler::setVar('safe_html_search', $safe_html_search);
    $handler::setVar('tabs', $tabs);
    $handler::setVar('list', $list);
    if ($handler::getCond('content_manager')) {
        $handler::setVar('user_items_editor', false);
    }
    $handler::setVar('share_links_array', CHV\render\get_share_links());
};
