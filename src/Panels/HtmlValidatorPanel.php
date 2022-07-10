<?php
/*
 EvoNext CMS Tracy
 Copyright (c) 2022
 Licensed under MIT License
 */

namespace EvoNext\Tracy\Panels;

use DOMDocument;
use EvoNext\Tracy\Contracts\IAjaxPanel;
use EvoNext\Tracy\Events\BeforeBarRender;
use LibXMLError;

class HtmlValidatorPanel extends AbstractSubscribePanel implements IAjaxPanel
{
    /**
     * $ignoreErrors.
     * @var array
     */
    protected static $ignoreErrors = [
        // XML_ERR_ENTITYREF_SEMICOL_MISSING
        23,
        // XML_HTML_UNKNOWN_TAG
        801,
    ];
    /**
     * $severenity.
     * @var array
     */
    protected static $severenity = [
        LIBXML_ERR_WARNING => 'Warning',
        LIBXML_ERR_ERROR   => 'Error',
        LIBXML_ERR_FATAL   => 'Fatal error',
    ];
    /**
     * $html.
     * @var string
     */
    protected $html;

    /**
     * setHTML.
     *
     * @param string $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes(): array
    {
        libxml_use_internal_errors(true);
        $dom                      = new DOMDocument('1.0', 'UTF-8');
        $dom->resolveExternals    = false;
        $dom->validateOnParse     = true;
        $dom->preserveWhiteSpace  = false;
        $dom->strictErrorChecking = true;
        $dom->recover             = true;

        @$dom->loadHTML($this->normalize($this->html));

        $errors = array_filter(libxml_get_errors(), function (LibXMLError $error) {
            return in_array((int)$error->code, static::$ignoreErrors, true) === false;
        });

        libxml_clear_errors();

        return [
            'severenity' => static::$severenity,
            'counter'    => count($errors),
            'errors'     => $errors,
            'html'       => $this->html,
        ];
    }

    /**
     * subscribe.
     **/
    protected function subscribe()
    {
        $events = $this->laravel['events'];
        $events->listen(BeforeBarRender::class, function ($barRender) {
            $this->setHtml($barRender->response->getContent());
        });
    }

    /**
     * Removes special controls characters and normalizes line endings and spaces.
     *
     * @param string $str
     * @return string
     */
    protected static function normalize($str)
    {
        $str = static::normalizeNewLines($str);
        // remove control characters; leave \t + \n
        $str = preg_replace('#[\x00-\x08\x0B-\x1F\x7F-\x9F]+#u', '', $str);
        // right trim
        $str = preg_replace('#[\t ]+$#m', '', $str);
        // leading and trailing blank lines
        $str = trim($str, "\n");

        return $str;
    }

    /**
     * Standardize line endings to unix-like.
     *
     * @param string $s
     * @return string
     */
    protected static function normalizeNewLines($s)
    {
        return str_replace(["\r\n", "\r"], "\n", $s);
    }
}
