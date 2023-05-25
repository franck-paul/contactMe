<?php
/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Jean-Christian Denis, Franck Paul and contributors
 *
 * @copyright Jean-Christian Denis, Franck Paul
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use dcCore;
use dcPage;

/**
 * Plugin definitions
 */
class My
{
    /**
     * This module id
     */
    public static function id(): string
    {
        return basename(dirname(__DIR__));
    }

    /**
     * This module name
     */
    public static function name(): string
    {
        return __((string) dcCore::app()->plugins->moduleInfo(self::id(), 'name'));
    }

    /**
     * This module directory path
     */
    public static function path(): string
    {
        return dirname(__DIR__);
    }

    // Contexts

    /** @var int Install context */
    public const INSTALL = 0;

    /** @var int Prepend context */
    public const PREPEND = 1;

    /** @var int Frontend context */
    public const FRONTEND = 2;

    /** @var int Backend context (usually when the connected user may access at least one functionnality of this module) */
    public const BACKEND = 3;

    /** @var int Manage context (main page of module) */
    public const MANAGE = 4;

    /** @var int Config context (config page of module) */
    public const CONFIG = 5;

    /** @var int Menu context (adding a admin menu item) */
    public const MENU = 6;

    /** @var int Widgets context (managing blog's widgets) */
    public const WIDGETS = 7;

    /** @var int Uninstall context */
    public const UNINSTALL = 8;

    /**
     * Check permission depending on given context
     *
     * @param      int   $context  The context
     *
     * @return     bool  true if allowed, else false
     */
    public static function checkContext(int $context): bool
    {
        switch ($context) {
            case self::INSTALL:
                // Installation of module
                // ----------------------
                // In almost all cases, only super-admin should be able to install a module

                return defined('DC_CONTEXT_ADMIN')
                    && dcCore::app()->auth->isSuperAdmin()   // Super-admin only
                    && dcCore::app()->newVersion(self::id(), dcCore::app()->plugins->moduleInfo(self::id(), 'version'))
                ;

            case self::UNINSTALL:
                // Uninstallation of module
                // ------------------------
                // In almost all cases, only super-admin should be able to uninstall a module

                return defined('DC_RC_PATH')
                    && dcCore::app()->auth->isSuperAdmin()   // Super-admin only
                ;

            case self::PREPEND:
                // Prepend context
                // ---------------

                return defined('DC_RC_PATH')
                ;

            case self::FRONTEND:
                // Frontend context
                // ----------------

                return defined('DC_RC_PATH')
                ;

            case self::BACKEND:
                // Backend context
                // ---------------
                // As soon as a connected user should have access to at least one functionnality of the module
                // Note that PERMISSION_ADMIN implies all permissions on current blog

                return defined('DC_CONTEXT_ADMIN')
                    // Check specific permission
                    && dcCore::app()->blog && dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                        dcCore::app()->auth::PERMISSION_ADMIN,  // Admin+
                    ]), dcCore::app()->blog->id)
                ;

            case self::MANAGE:
                // Main page of module
                // -------------------
                // In almost all cases, only blog admin and super-admin should be able to manage a module

                return defined('DC_CONTEXT_ADMIN')
                    // Check specific permission
                    && dcCore::app()->blog && dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                        dcCore::app()->auth::PERMISSION_ADMIN,  // Admin+
                    ]), dcCore::app()->blog->id)
                ;

            case self::CONFIG:
                // Config page of module
                // ---------------------
                // In almost all cases, only super-admin should be able to configure a module

                return defined('DC_CONTEXT_ADMIN')
                    && dcCore::app()->auth->isSuperAdmin()   // Super-admin only
                ;

            case self::MENU:
                // Admin menu
                // ----------
                // In almost all cases, only blog admin and super-admin should be able to add a menuitem if
                // the main page of module is used for configuration, but it may be necessary to modify this
                // if the page is used to manage anything else

                return defined('DC_CONTEXT_ADMIN')
                    // Check specific permission
                    && dcCore::app()->blog && dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                        dcCore::app()->auth::PERMISSION_ADMIN,  // Admin+
                    ]), dcCore::app()->blog->id)
                ;

            case self::WIDGETS:
                // Blog widgets
                // ------------
                // In almost all cases, only blog admin and super-admin should be able to manage blog's widgets

                return defined('DC_CONTEXT_ADMIN')
                    // Check specific permission
                    && dcCore::app()->blog && dcCore::app()->auth->check(dcCore::app()->auth->makePermissions([
                        dcCore::app()->auth::PERMISSION_ADMIN,  // Admin+
                    ]), dcCore::app()->blog->id)
                ;
        }

        return false;
    }

    /**
     * Return array of module icon(s)
     *
     * [light_mode_icon_url, dark_mode_icon_url] or [both_modes_icon_url]
     *
     * @return     array<string>
     */
    public static function icons(): array
    {
        // Comment second line if you only have one icon.svg for both mode
        return [
            urldecode(dcPage::getPF(self::id() . '/icon.svg')),         // Light (or both) mode(s)
            // urldecode(dcPage::getPF(self::id() . '/icon-dark.svg')),    // Dark mode
        ];
    }

    /**
     * Return URL regexp scheme cope by the plugin
     *
     * @return     string
     */
    public static function urlScheme(): string
    {
        return '/' . preg_quote(dcCore::app()->adminurl->get('admin.plugin.' . self::id())) . '(&.*)?$/';
    }

    /**
     * Makes an url including optionnal parameters.
     *
     * @param      array   $params  The parameters
     *
     * @return     string
     */
    public static function makeUrl(array $params = []): string
    {
        return dcCore::app()->adminurl->get('admin.plugin.' . self::id(), $params);
    }
}
