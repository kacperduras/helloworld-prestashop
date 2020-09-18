<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Helloworld extends Module {

    protected $config_form = false;

    public function __construct() {
        $this->name = 'helloworld';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'Kacper Duras';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Hello World');
        $this->description = $this->l('Great module for PrestaShop');

        $this->confirmUninstall = $this->l('Are you want to uninstall the module?');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    public function install() {
        Configuration::updateValue('HELLOWORLD_LIVE_MODE', false);

        return parent::install();
    }

    public function uninstall() {
        Configuration::deleteByName('HELLOWORLD_LIVE_MODE');

        return parent::uninstall();
    }

    public function getContent() {
        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        if (((bool)Tools::isSubmit('submitHelloworldModule')) == true) {
            $this->postProcess();
            $output .= $this->displayConfirmation('Settings updated');
        }

        return $output.$this->renderForm();
    }

    protected function renderForm() {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitHelloworldModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm() {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'HELLOWORLD_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    protected function getConfigFormValues() {
        return array(
            'HELLOWORLD_LIVE_MODE' => Configuration::get('HELLOWORLD_LIVE_MODE', true)
        );
    }

    protected function postProcess() {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

}
