<?php

use Broarm\CookieConsent\CookieConsentForm;
use SilverStripe\Core\Manifest\ModuleLoader;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\Requirements;

/**
 * Class CookiePolicyPageController
 * @mixin CookiePolicyPage
 */
class CookiePolicyPageController extends PageController
{
    private static $allowed_actions = array(
        'Form'
    );

    /**
     * Make sure we don't trigger the cookie consent popup on this page
     *
     * @return HTTPResponse|void
     */
    public function init()
    {
        parent::init();
        Requirements::block(
            ModuleLoader::getModule('bramdeleeuw/cookieconsent')
                ->getResource('javascript/dist/cookieconsentpopup.js')
                ->getRelativePath()
        );
    }

    /**
     * Using $CookieConsentForm in the Content area of the page shows
     * where the form should be rendered into. If it does not exist
     * then default back to $Form.
     *
     * Blatantly ripped off with love from UserDefinedForm
     *
     * @return array
     */
    public function index()
    {
        if ($this->Content && $form = $this->Form()) {
            $hasLocation = stristr($this->Content, '$CookieConsentForm');
            if ($hasLocation) {
                $content = preg_replace('/(<p[^>]*>)?\\$CookieConsentForm(<\\/p>)?/i', $form->forTemplate(), $this->Content);
                return array(
                    'Content' => DBField::create_field('HTMLText', $content),
                    'Form' => ""
                );
            }
        }

        return array(
            'Content' => DBField::create_field('HTMLText', $this->Content),
            'Form' => $this->Form()
        );
    }

    /**
     * Get the CookieConsentForm
     *
     * @return CookieConsentForm
     */
    public function Form()
    {
        return CookieConsentForm::create($this, 'Form');
    }
}
