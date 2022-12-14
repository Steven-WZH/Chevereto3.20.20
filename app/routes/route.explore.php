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

    if (!$handler::getCond('explore_enabled') && !$logged_user['is_content_manager']) {
        return $handler->issue404();
    }

    $doing = $handler->request[0] ?? null;

    if (!isset($doing) && CHV\getSetting('homepage_style') == 'route_explore' && strpos(G\get_current_url(), G\get_base_url(G\get_route_name())) !== false) {
        $redir = G\str_replace_first(G\get_base_url(G\get_route_name()), G\get_base_url(), G\get_current_url());
        G\redirect($redir);
    }

    $explore_semantics = $handler::getVar('explore_semantics');

    if (isset($doing) && !array_key_exists($doing, $explore_semantics)) {
        return $handler->issue404();
    }

    if ($handler->isRequestLevel(3)) {
        return $handler->issue404();
    } // Allow only 3 levels

    $basename = CHV\getSetting('homepage_style') == 'route_explore' && $handler->getCond('mapped_route') ? null : G\get_route_name();
    if ($doing) {
        $basename .= ($basename ? '/' : null) . $doing;
    }

    $listing = isset($doing) ? $explore_semantics[$doing] : ['label' => _s('Explore'), 'icon' => 'fas fa-images'];
    $listing['list'] = is_null($doing) ? G\get_route_name() : $doing;

    $listingParams = [
        'listing'    => $listing['list'],
        'basename'    => $basename,
        'params_hidden' => [
            'hide_empty' => 1,
            'hide_banned' => 1,
            'album_min_image_count' => CHV\getSetting('explore_albums_min_image_count'),
        ],
    ];

    if ($doing == 'animated') {
        $listingParams['params_hidden']['is_animated'] = 1;
    }

    $tabs = CHV\Listing::getTabs($listingParams, true);
    $currentKey = $tabs['currentKey'];
    $type = $tabs['tabs'][$currentKey]['type'];
    $tabs = $tabs['tabs'];

    parse_str($tabs[$currentKey]['params'], $tabs_params);

    $list_params = CHV\Listing::getParams(); // Use CHV magic params
    $list_params['sort'] = explode('_', $tabs_params['sort']); // Hack this stuff
    $handler::setVar('list_params', $list_params);

    // List
    $list = new CHV\Listing;
    $list->setType($type);
    if(isset($list_params['reverse'])) {
        $list->setReverse($list_params['reverse']);
    }
    if(isset($list_params['seek'])) {
        $list->setSeek($list_params['seek']);
    }
    $list->setOffset($list_params['offset']);
    $list->setLimit($list_params['limit']); // how many results?
    $list->setItemsPerPage($list_params['items_per_page']); // must
    $list->setSortType($list_params['sort'][0]); // date | size | views | likes
    $list->setSortOrder($list_params['sort'][1]); // asc | desc
    $list->setRequester(CHV\Login::getUser());
    $list->setParamsHidden($listingParams['params_hidden']);
    $list->exec();

    $handler::setVar('listing', $listing);

    if (CHV\getSetting('homepage_style') == 'route_explore') {
        $handler::setVar('doctitle', CHV\Settings::get('website_doctitle'));
        $handler::setVar('pre_doctitle', CHV\Settings::get('website_name'));
    } else {
        $handler::setVar('pre_doctitle', _s('Explore') . ' ' . $listing['label']);
    }

    $handler::setVar('category', null);
    $handler::setVar('tabs', $tabs);
    $handler::setVar('list', $list);

    if (isset($logged_user['is_content_manager']) && $logged_user['is_content_manager']) {
        $handler::setVar('user_items_editor', false);
    }
    $handler::setVar('share_links_array', CHV\render\get_share_links());
};
