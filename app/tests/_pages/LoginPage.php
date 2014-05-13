<?php

class LoginPage
{
    // include url of current page
    static $URL = '/';
    static $rutField = 'input[name=rut]';
    static $dvField = 'input[name=dvrut]';
    static $passwordField = 'input[name=password]';
    static $submitButton = 'input[type=submit]';


    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: EditPage::route('/123-post');
     */
     public static function route($param)
     {
        return static::$URL.$param;
     }


}