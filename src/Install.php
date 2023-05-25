<?php
/**
 * @brief contactMe, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @author Franck Paul and contributors
 *
 * @copyright Franck Paul carnet.franck.paul@gmail.com
 * @copyright GPL-2.0 https://www.gnu.org/licenses/gpl-2.0.html
 */
declare(strict_types=1);

namespace Dotclear\Plugin\contactMe;

use dcCore;
use dcNamespace;
use dcNsProcess;
use Exception;

class Install extends dcNsProcess
{
    public static function init(): bool
    {
        static::$init = My::checkContext(My::INSTALL);

        return static::$init;
    }

    public static function process(): bool
    {
        if (!static::$init) {
            return false;
        }

        try {
            // Update
            $old_version = dcCore::app()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.0', '<')) {
                // Rename settings namespace
                if (dcCore::app()->blog->settings->exists('contactme')) {
                    dcCore::app()->blog->settings->delNamespace(My::id());
                    dcCore::app()->blog->settings->renNamespace('contactme', My::id());
                }

                // Change settings names (remove cm_ prefix in them)
                $rename = function (string $name, dcNamespace $settings): void {
                    if ($settings->settingExists('cm_' . $name, true)) {
                        $settings->rename('cm_' . $name, $name);
                    }
                };

                $settings = dcCore::app()->blog->settings->get(My::id());
                foreach ([
                    'recipients',
                    'subject_prefix',
                    'page_title',
                    'form_caption',
                    'msg_success',
                    'msg_error',
                    'smtp_account',
                    'use_antispam',
                ] as $value) {
                    $rename($value, $settings);
                }
            }

            // Init
            $settings = dcCore::app()->blog->settings->get(My::id());
            $settings->put('active', true, dcNamespace::NS_BOOL, 'Active', false, true);
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }

        return true;
    }
}
