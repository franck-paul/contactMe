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

use Dotclear\App;
use Dotclear\Core\Process;
use Dotclear\Interface\Core\BlogWorkspaceInterface;
use Exception;

class Install extends Process
{
    public static function init(): bool
    {
        return self::status(My::checkContext(My::INSTALL));
    }

    public static function process(): bool
    {
        if (!self::status()) {
            return false;
        }

        try {
            // Update
            $old_version = App::version()->getVersion(My::id());
            if (version_compare((string) $old_version, '3.0', '<')) {
                // Rename settings namespace
                if (App::blog()->settings()->exists('contactme')) {
                    App::blog()->settings()->delWorkspace(My::id());
                    App::blog()->settings()->renWorkspace('contactme', My::id());
                }

                // Change settings names (remove cm_ prefix in them)
                $rename = function (string $name, BlogWorkspaceInterface $settings): void {
                    if ($settings->settingExists('cm_' . $name, true)) {
                        $settings->rename('cm_' . $name, $name);
                    }
                };

                $settings = My::settings();
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
            $settings = My::settings();
            $settings->put('active', true, App::blogWorkspace()::NS_BOOL, 'Active', false, true);
        } catch (Exception $e) {
            App::error()->add($e->getMessage());
        }

        return true;
    }
}
