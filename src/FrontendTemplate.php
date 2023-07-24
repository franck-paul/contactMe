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
use dcTemplate;
use Dotclear\Helper\Html\Html;

class FrontendTemplate
{
    public static function ContactMeURL($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->url.dcCore::app()->url->getURLFor("contactme")') . '; ?>';
    }

    public static function ContactMeIf($attr, $content)
    {
        $if = [];

        $operator = isset($attr['operator']) ? dcTemplate::getOperator($attr['operator']) : '&&';

        if (isset($attr['sent'])) {
            $sign = (bool) $attr['sent'] ? '' : '!';
            $if[] = $sign . "dcCore::app()->ctx->contactme['sent']";
        }

        if (isset($attr['error'])) {
            $sign = (bool) $attr['error'] ? '' : '!';
            $if[] = $sign . "dcCore::app()->ctx->contactme['error']";
        }

        if (!empty($if)) {
            return '<?php if(' . implode(' ' . $operator . ' ', $if) . ') : ?>' . $content . '<?php endif; ?>';
        }

        return $content;
    }

    public static function ContactMePageTitle($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->settings->' . My::id() . '->page_title') . '; ?>';
    }

    public static function ContactMeFormCaption($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->blog->settings->' . My::id() . '->form_caption') . '; ?>';
    }

    public static function ContactMeMsgSuccess()
    {
        return '<?php echo dcCore::app()->blog->settings->' . My::id() . '->msg_success; ?>';
    }

    public static function ContactMeMsgError()
    {
        return '<?php echo sprintf(dcCore::app()->blog->settings->' . My::id() . '->msg_error,' . Html::class . '::escapeHTML(dcCore::app()->ctx->contactme["error_msg"])); ?>';
    }

    public static function ContactMeName($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["name"]') . '; ?>';
    }

    public static function ContactMeEmail($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["email"]') . '; ?>';
    }

    public static function ContactMeSite($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["site"]') . '; ?>';
    }

    public static function ContactMeSubject($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["subject"]') . '; ?>';
    }

    public static function ContactMeMessage($attr)
    {
        $f = dcCore::app()->tpl->getFilters($attr);

        return '<?php echo ' . sprintf($f, 'dcCore::app()->ctx->contactme["message"]') . '; ?>';
    }
}
