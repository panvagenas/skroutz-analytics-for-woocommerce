<?php
/**
 * Copyright (C) 2016 Panagiotis Vagenas <pan.vagenas@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* -- WordPress® -------------------------------------------------------------------------------------------------------

Version: 0.1
Stable tag: 0.1
Tested up to: 4.4.1
Requires at least: 4.0

Requires at least Apache version: 2.1
Tested up to Apache version: 2.4.7

Requires at least PHP version: 5.4
Tested up to PHP version: 5.5.12

Copyright: © 2016 Panagiotis Vagenas <pan.vagenas@gmail.com
License: GNU General Public License V3
Contributors: pan.vagenas

Author: Panagiotis Vagenas <pan.vagenas@gmail.com>
Author URI: http://gr.linkedin.com/in/panvagenas

Text Domain: woocommerce-skrouz-analytics
Domain Path: /lang

Plugin Name: Skroutz Analytics for WooCommerce
Plugin URI: https://github.com/panvagenas/woocommerce-skroutz-analytics

Description:

Tags: skroutz, skroutz.gr, analytics

-- end section for WordPress®. -------------------------------------------------------------------------------------- */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once "vendor/autoload.php";

(new \Pan\SkroutzAnalytics\Initializer(__FILE__))->run();