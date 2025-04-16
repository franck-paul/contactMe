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

use ArrayObject;
use Dotclear\Core\Frontend\Tpl;
use Dotclear\Plugin\TemplateHelper\Code;

class FrontendTemplate
{
    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeURL(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeURL(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     * @param      string                                            $content   The content
     */
    public static function ContactMeIf(array|ArrayObject $attr, string $content): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        $if       = [];
        $operator = isset($attr['operator']) ? Tpl::getOperator($attr['operator']) : '&&';

        if (isset($attr['sent'])) {
            $sign = (bool) $attr['sent'] ? '' : '!';
            $if[] = $sign . "App::frontend()->context()->contactme['sent']";
        }

        if (isset($attr['error'])) {
            $sign = (bool) $attr['error'] ? '' : '!';
            $if[] = $sign . "App::frontend()->context()->contactme['error']";
        }

        if ($if !== []) {
            return Code::getPHPTemplateBlockCode(
                FrontendTemplateCode::ContactMeIf(...),
                [
                    implode(' ' . $operator . ' ', $if),
                ],
                $content,
                $attr,
            );
        }

        return $content;
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMePageTitle(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMePageTitle(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeFormCaption(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeFormCaption(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeMsgSuccess(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeMsgSuccess(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeMsgError(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeMsgError(...),
            [
                My::id(),
            ],
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeName(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeName(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeEmail(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeEmail(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeSite(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeSite(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeSubject(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeSubject(...),
            attr: $attr,
        );
    }

    /**
     * @param      array<string, mixed>|\ArrayObject<string, mixed>  $attr      The attribute
     */
    public static function ContactMeMessage(array|ArrayObject $attr): string
    {
        $attr = $attr instanceof ArrayObject ? $attr : new ArrayObject($attr);

        return Code::getPHPTemplateValueCode(
            FrontendTemplateCode::ContactMeMessage(...),
            attr: $attr,
        );
    }
}
