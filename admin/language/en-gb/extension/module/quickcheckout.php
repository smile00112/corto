<?php
// Heading
$_['page_title']    						= 'Custom Quick Checkout';
$_['heading_title']    = '<a href="https://opencart3x.ru" target="_blank" title="Разработчик Opencart3x.ru" style="color:#233746"><i class="fa fa-circle-o"></i></a> '. $_['page_title'];

// Tab
$_['tab_home']								= 'Home';
$_['tab_general']							= 'General';
$_['tab_design']							= 'Design';
$_['tab_field']								= 'Fields';
$_['tab_module']							= 'Modules';
$_['tab_payment']							= 'Payment';
$_['tab_shipping']							= 'Shipping';
$_['tab_survey']							= 'Survey';
$_['tab_delivery']							= 'Delivery';
$_['tab_countdown']							= 'Countdown';
$_['tab_analytics']							= 'Analytics';

// Text
$_['text_default_store']					= 'Default Store';
$_['text_module']      						= 'Modules';
$_['text_success']     						= 'Success: You have modified module Custom Quick Checkout!';
$_['text_edit']								= 'Edit Module Custom Quick Checkout';
$_['text_general']							= 'Configure general settings to get started.';
$_['text_design']							= 'Configure the checkout design and layout here.';
$_['text_field']							= 'Configure your checkout fields here.';
$_['text_module_home']						= 'Configure the various checkout module boxes here.';
$_['text_payment']							= 'Configure your payment module box here.';
$_['text_shipping']							= 'Configure your shipping module box here.';
$_['text_survey']							= 'Configure your survey questions here.';
$_['text_delivery']							= 'Configure your delivery feature here.';
$_['text_countdown']						= 'Configure your countdown timer here.';
$_['text_analytics']						= 'Track abandoned orders with purchase of our Recover Abandoned Cart extension.';
$_['text_radio_type']						= 'Radio Type';
$_['text_select_type']						= 'Select Type';
$_['text_text_type']						= 'Text Type';
$_['text_one_column']						= 'One Column';
$_['text_two_column']						= 'Two Column';
$_['text_three_column']						= 'Three Column';
$_['text_estimate']							= 'Estimate';
$_['text_choose']							= 'Choose';
$_['text_day']								= 'Everyday';
$_['text_specific']							= 'Specific Date';
$_['text_display']							= 'Display';
$_['text_required']							= 'Required';
$_['text_default']							= 'Default Value';
$_['text_placeholder']						= 'Placeholder';
$_['text_sort_order']						= 'Sort Order';
$_['text_purchase_analytics']				= 'Purchase our Recover Abandoned Cart module to track abandoned orders and more.';
$_['text_spinner']							= 'Spinner';
$_['text_overlay']							= 'Overlay';
$_['text_help_grid']						= '<i class="fa fa-info"></i> Drag&drop the blocks or change the width of the columns to change design of the checkout page';
$_['text_method_images']					= 'Images for methods';
$_['text_depends_payment']					= 'Payment depends on shipping';

// Help
$_['help_status']							= 'Main function to turn on/off this extension.';
$_['help_confirmation_page']				= 'Enable the confirmation page or disable it. Disable to remove the confirmation page completely.';
$_['help_load_screen']						= 'Sets the status of the loading screen when the checkout is loading.';
$_['help_loading_display']					= 'Sets either a spinner or overlay when the modules are loading.';
$_['help_payment_logo']						= 'Display the payment logo for payment methods. This only works when payment method is displayed in radio mode.';
$_['help_shipping_logo']					= 'Display the shipping logo for shipping methods. This only works when shipping method is displayed in radio mode.';
$_['help_payment']							= 'Sets the mode payment methods are displayed in. Either radio buttons or select drop down.';
$_['help_shipping']							= 'Sets the mode shipping methods are displayed in. Either radio buttons or select drop down.';
$_['help_edit_cart']						= 'Sets whether the user can edit their cart at the checkout. Only available when cart module is enabled.';
$_['help_highlight_error']					= 'Sets whether fields with errors should be highlighted to the user.';
$_['help_text_error']						= 'Set whether fields with errors should have the error message displayed right below the field.';
$_['help_layout']							= 'Sets the layout style of the checkout. Available in one, two, or three columns layout.';
$_['help_slide_effect']						= 'Sliding effect for transition between details and order confirmation page. Only when confirmation page is enabled.';
$_['help_minimum_order']					= 'Minimum order amount before checkout can be activated.';
$_['help_save_data']						= 'Automatically save data customer enters during checkout.';
$_['help_debug']							= 'Turn on debug mode for checkout. Only turn this on if you know what you are doing.';
$_['help_auto_submit']						= 'System attempts to skip order confirmation page for payment methods without the need to fill in additional details on the confirmation page. Disable this function if you are unable to complete the checkout or payment requires additional details on the confirmation page. Automatically disabled if order confirmation page is disabled.';
$_['help_payment_target']					= 'The target ID of the button for the payment modules required for auto submit.';
$_['help_proceed_button_text']				= 'The text to display for the proceed button on the checkout page.';
$_['help_responsive']						= 'Only select this if you are using a responsive theme.';
$_['help_payment_reload']					= 'Only enable if your payment methods have surcharges. Disable to reduce ajax requests.';
$_['help_shipping_reload']					= 'Only enable if your payment methods are dependent on your shipping methods. Disable to reduce ajax requests.';
$_['help_coupon']		 					= 'Turn on/off the coupon module on the checkout page.';
$_['help_voucher']		 					= 'Turn on/off the voucher module on the checkout page.';
$_['help_reward']		 					= 'Turn on/off the reward module on the checkout page.';
$_['help_cart']								= 'Turn on/off the cart module on the checkout page.';
$_['help_shipping_module']					= 'Turn on/off the shipping method module on the checkout page.';
$_['help_payment_module']					= 'Turn on/off the payment method module on the checkout page.';
$_['help_payment_default']					= 'Set default payment method module selected.';
$_['help_shipping_default']					= 'Set default shipping method module selected.';
$_['help_login_module']						= 'Turn on/off the login module on the checkout page.';
$_['help_html_header']						= 'Add custom HTML contents to the header of the checkout page.';
$_['help_html_footer']						= 'Add custom HTML contents to the footer of the checkout page.';
$_['help_survey']      						= 'Turn on/off the survey function on the checkout page.';
$_['help_survey_required']					= 'Sets whether the survey field should be a required field.';
$_['help_survey_text']  					= 'Sets the text to display on the checkout page. Multi-language supported.';
$_['help_survey_type']  					= 'Sets the answer type for the checkout. Either open ended text field or select drop down list.';
$_['help_delivery']							= 'Turn on/off the delivery function on the checkout page.';
$_['help_delivery_time']					= 'Only available when the delivery function is enabled.';
$_['help_delivery_required'] 				= 'Sets whether the delivery field should be a required field.';
$_['help_delivery_unavailable']				= 'Exclude the listed dates from delivery. &quot;yyyy-mm-dd&quot;, &quot;yyyy-mm-dd&quot; format, within inverted quotes and also comma separated. Leading zeroes.';
$_['help_delivery_min']  					= 'The minimum number of days away from the current date allowed for delivery.';
$_['help_delivery_max']  					= 'The maximum number of days away from the current date allowed for delivery.';
$_['help_delivery_min_hour']  				= 'The earliest hour for delivery.';
$_['help_delivery_max_hour']  				= 'The latest hour for delivery.';
$_['help_delivery_days_of_week']			= 'The days of week to exclude. 0, 1, 6 format, comma separated. (0 to 6 only)';
$_['help_delivery_times']					= 'Time range you wish to allow for delivery.';
$_['help_countdown']						= 'Turn on/off the countdown function on the checkout page.';
$_['help_countdown_start']					= 'Sets when the countdown timer should restart.';
$_['help_countdown_date_start']				= 'Sets the start date for the timer.';
$_['help_countdown_date_end']				= 'Sets the end date for the timer';
$_['help_countdown_time']					= 'Sets the time for the timer to reset daily. Time in 24 hours format. (e.g. 12:00)';
$_['help_countdown_text']					= 'The text to display for the timer. Use the variable {timer} to place the timer in.';
$_['help_display_more']						= 'Install and enable modules to display more.';
$_['help_keyword']							= 'Set URL alias for each language.';
$_['help_skip_cart']						= 'Bypass Cart Page';
$_['help_force_bootstrap']					= 'Force embedding Bootstrap script';
$_['help_show_shipping_address']			= 'Show Shipping Address (for logged users)?';

$_['help_login'] = 'Login block where a customer can login or select a checkout option.';
$_['help_payment_address'] = 'Customer information and payment address.';
$_['help_shipping_address'] = 'Extra address for shipping purposes.';
$_['help_shipping_method'] = 'Third step. You can set a default method and hide this step.';
$_['help_payment_method'] = 'Fourth step. You can set a default method and hide this step.';
$_['help_cart'] = 'Part of the last step - the cart. You can move to the top as well.';
$_['help_coupons'] = 'Coupons, reward points, voucher apply fields';
$_['help_confirm'] = 'The last step is the confirm. Edit fields.';
$_['text_login'] = 'Login';
$_['text_payment_address'] = 'Payment Address';
$_['text_shipping_address'] = 'Shipping Address';
$_['text_shipping_method'] = 'Shipping Method';
$_['text_payment_method'] = 'Payment Method';
$_['text_coupons'] = 'Coupon, Voucher, Points';
$_['text_cart'] = 'Cart';
$_['text_confirm'] = 'Confirm';
$_['text_custom_column'] = 'Custom design';

// General
$_['entry_store']							= 'Configure Store:';
$_['entry_status']     					    = 'Status';
$_['entry_minimum_order']					= 'Minimum Order Amount';
$_['entry_debug']							= 'Debug';
$_['entry_confirmation_page']     		    = 'Confirmation Page';
$_['entry_save_data']						= 'Auto Save Data';
$_['entry_edit_cart']						= 'Allow Users to Edit Cart Quantities';
$_['entry_highlight_error']					= 'Highlight Fields with Error';
$_['entry_text_error']						= 'Display Error Below Fields';
$_['entry_auto_submit']					 	= 'Attempt Auto Submit';
$_['entry_payment_target']					= 'Payment Button Targets';
$_['entry_proceed_button_text']				= 'Proceed Button Text';
$_['entry_keyword']							= 'SEO URL';
$_['entry_skip_cart']						= 'Skip Cart Page';
$_['entry_force_bootstrap']					= 'Force Bootstrap';

// Design
$_['entry_load_screen']  					= 'Display Load Screen';
$_['entry_loading_display']					= 'Loading Display';
$_['entry_layout']							= 'Layout';
$_['entry_responsive']						= 'Responsive Quick Checkout';
$_['entry_slide_effect']					= 'Slide Effect';
$_['entry_custom_css']						= 'Custom CSS Codes';
$_['entry_show_shipping_address']			= 'Shipping Address';

// Field
$_['entry_field_firstname']    				= 'First Name';
$_['entry_field_lastname']     				= 'Last Name';
$_['entry_field_email']     				= 'Email';
$_['entry_field_telephone']    				= 'Telephone';
$_['entry_field_company']      				= 'Company';
$_['entry_field_customer_group']			= 'Customer Group';
$_['entry_field_address_1']    				= 'Address 1';
$_['entry_field_address_2']    				= 'Address 2';
$_['entry_field_city']    					= 'City';
$_['entry_field_postcode']    				= 'Postcode';
$_['entry_field_country']     				= 'Country';
$_['entry_field_zone']     					= 'Region / State';
$_['entry_field_newsletter'] 	  		  	= 'Newsletter Checkbox';
$_['entry_field_register'] 		  		  	= 'Register Checkbox';
$_['entry_field_shipping'] 		  		  	= 'Shipping address is the same';
$_['entry_field_rules'] 		  		  	= 'Checkout Policy';
$_['entry_field_comment'] 		  		  	= 'Order Comments';

// Module
$_['entry_coupon']		 					= 'Display Coupon Module';
$_['entry_voucher']		 					= 'Display Voucher Module';
$_['entry_reward']		 					= 'Display Reward Module';
$_['entry_cart']							= 'Display Cart Module';
$_['entry_login_module']					= 'Display Login Module';
$_['entry_html_header']						= 'Custom HTML Header';
$_['entry_html_footer']						= 'Custom HTML Footer';

// Payment
$_['entry_payment_module']					= 'Display Payment Method Module';
$_['entry_payment_reload']					= 'Payment Reloads Cart';
$_['entry_payment']    					    = 'Payment Selection';
$_['entry_payment_default']					= 'Default Payment Module';
$_['entry_payment_logo']					= 'Payment Logo URL';

// Shipping
$_['entry_shipping_module']					= 'Display Shipping Method Module';
$_['entry_shipping_reload']					= 'Payment depends by Shipping';
$_['entry_shipping']    					= 'Shipping Selection';
$_['entry_shipping_default']				= 'Default Shipping Module';
$_['entry_shipping_logo']					= 'Shipping Logo URL';
$_['entry_shipping_title_display']			= 'Show group title';

// Survey
$_['entry_survey']      					= 'Display Survey Field';
$_['entry_survey_required']					= 'Survey Required';
$_['entry_survey_text']  					= 'Survey Question';
$_['entry_survey_type']  					= 'Survey Type';
$_['entry_survey_answer']  					= 'Survey Answers';

// Delivery
$_['entry_delivery']						= 'Display Delivery Field';
$_['entry_delivery_time']					= 'Display Delivery Time';
$_['entry_delivery_required'] 				= 'Delivery Date Required';
$_['entry_delivery_unavailable']			= 'Delivery Dates Unavailable';
$_['entry_delivery_min']  					= 'Delivery Minimum Days in Advance';
$_['entry_delivery_max']  					= 'Delivery Maximum Days in Advance';
$_['entry_delivery_min_hour']				= 'Delivery Earliest Hour';
$_['entry_delivery_max_hour']				= 'Delivery Latest Hour';
$_['entry_delivery_days_of_week']			= 'Delivery Days of Week to Disable';
$_['entry_delivery_times']					= 'Delivery Times';

// Countdown
$_['entry_countdown']						= 'Countdown Timer';
$_['entry_countdown_start']					= 'Countdown Starts';
$_['entry_countdown_date_start']			= 'Start Date';
$_['entry_countdown_date_end']				= 'End Date';
$_['entry_countdown_time']					= 'Countdown Time';
$_['entry_countdown_text']					= 'Countdown Text';

// Button
$_['button_add']							= 'Add';
$_['button_continue']						= 'Save and Continue';
$_['button_remove']							= 'Remove';

// Error
$_['error_permission'] 						= 'Warning: You do not have permission to modify module Custom Quick Checkout!';