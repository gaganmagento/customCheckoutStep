define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Checkout/js/model/step-navigator',
    'mage/url',
    'mage/translate'
], function (ko, Component, _,stepNavigator,url,$t) {
    'use strict';

    var configValues = window.checkoutConfig;
    /**
     * mystep - is the name of the component's .html template,
     * Webkul_CheckoutCustomSteps  - is the name of your module directory.
     */
     
    return Component.extend({
        defaults: {
            template: 'Checkout_Step/check-new'
        },
        // add here your logic to display step,
        isVisible: ko.observable(true),
        errorValidationMessage: ko.observable(false),
        /**
         * @returns {*}
         */

        initialize: function () {
        this.address = ko.observable().extend({
                required: true
        });
            this._super();
            // register your step
            
            
            
            function checkNested(obj /*, level1, level2, ... levelN*/) {
                var args = Array.prototype.slice.call(arguments, 1);

                for (var i = 0; i < args.length; i++) {
                  if (!obj || !obj.hasOwnProperty(args[i])) {
                    return false;
                  }
                  obj = obj[args[i]];
                }
                return true;
              }
            
            
            if(checkNested(checkoutConfig, 'customerData', 'custom_attributes', 'elmag_customer_kind', 'value') && checkNested(configValues, 'customerData')) {
                
                if(configValues.customerData != 0){
                
                    if(checkoutConfig.customerData.custom_attributes.elmag_customer_kind.value !== 'dealer' || checkoutConfig.customerData.custom_attributes.elmag_dealerview_visible.value !== '4'){
                        stepNavigator.registerStep(
                            // step code will be used as step content id in the component template
                            'dealer_choice',
                            // step alias
                            null,
                            // step title value
                            $t('Dealer Choice'),
                            // observable property with logic when display step or hide step
                            this.isVisible,
                            _.bind(this.navigate, this),
                            /**
                             * sort order value
                             * 'sort order value' < 10: step displays before shipping step;
                             * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                             * 'sort order value' > 20 : step displays after payment step
                             */
                            9
                        );
                    }
                } else {
                    
                    stepNavigator.registerStep(
                        // step code will be used as step content id in the component template
                        'dealer_choice',
                        // step alias
                        null,
                        // step title value
                        $t('Dealer Choice'),
                        // observable property with logic when display step or hide step
                        this.isVisible,
                        _.bind(this.navigate, this),
                        /**
                         * sort order value
                         * 'sort order value' < 10: step displays before shipping step;
                         * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                         * 'sort order value' > 20 : step displays after payment step
                         */
                        9
                    );
                    
                    
                }
        }else{
            stepNavigator.registerStep(
                        // step code will be used as step content id in the component template
                        'dealer_choice',
                        // step alias
                        null,
                        // step title value
                        $t('Dealer Choice'),
                        // observable property with logic when display step or hide step
                        this.isVisible,
                        _.bind(this.navigate, this),
                        /**
                         * sort order value
                         * 'sort order value' < 10: step displays before shipping step;
                         * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                         * 'sort order value' > 20 : step displays after payment step
                         */
                        9
            );
        }
        
        setTimeout(function(){
            if(checkNested(checkoutConfig, 'customerData', 'custom_attributes', 'elmag_customer_kind', 'value') && checkNested(configValues, 'customerData')) {
                if(configValues.customerData != 0){
                    if(checkoutConfig.customerData.custom_attributes.elmag_customer_kind.value == 'dealer'
                     && checkoutConfig.customerData.custom_attributes.elmag_dealerview_visible.value == '4'){
                     jQuery('#step_code').addClass('hidemapcustom');
                    }
                }
            }
   }, 1000);
            return this;
        },

        
        /**
         * The navigate() method is responsible for navigation between checkout steps
         * during checkout. You can add custom logic, for example some conditions
         * for switching to your custom step
         * When the user navigates to the custom step via url anchor or back button we_must show step manually here
         */
        navigate: function () {
            this.isVisible(true);
        },
        getBaseUrl: function() {
            return url.build('customer/account/login');
        },
        /**
         * @returns void
         */
        navigateToNextStep: function () {
        require(['jquery', 'jquery/ui',  'mage/url'], function($){  
                var linkUrl = url.build('map/index/save');
                console.log(configValues.dealer);
                if(configValues.dealer == '' || configValues.dealer == undefined){
                    var customer = jQuery('#select-dealer-drop').val();
                    
                }else{
                var customer = configValues.dealer;
            }
                $.ajax({
                    method: "POST",
                    url: linkUrl,
                    data: {key: 'dealer',customer: customer},
                    dataType: "json",
                    showLoader: true,
                }).done(function( response ) {
                   stepNavigator.next();
                });
            });
        }
     });
    });
    