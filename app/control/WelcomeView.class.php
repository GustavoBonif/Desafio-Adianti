<?php
/**
 * WelcomeView
 *
 * @version    1.0
 * @package    control
 * @author     Pablo Dall'Oglio
 * @copyright  Copyright (c) 2006 Adianti Solutions Ltd. (http://www.adianti.com.br)
 * @license    http://www.adianti.com.br/framework-license
 */
class WelcomeView extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $html = new THtmlRenderer('app/resources/system_welcome_pt.html');

        // replace the main section variables
        $html->enableSection('main', array());
        
        $panel = new TPanelGroup('Bem-vindo!');
        $panel->add($html);
		
        $vbox = TVBox::pack($panel);
        $vbox->style = 'display:block; width: 100%';
        
        // add the template to the page
        parent::add( $vbox );
    }
}
