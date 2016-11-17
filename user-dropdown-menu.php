<?php

/**
 * @package User Dropdown Menu
 */
/*
Plugin Name: User Dropdown Menu
Plugin URI: http://www.sagaio.com/moduler/
Description: Insert a dropdown menu with a icon button, based on Bootstrap 4.0 Dropdown. All settings can be found in the Theme Customizer.
Version: 1.0.1
Author: SAGAIO
Author URI: http://www.sagaio.com
License: GPLv2 or later
Licemse URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html

User Dropdown Menu is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

User Dropdown Menu is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with User Dropdown Menu. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
*/

defined( 'ABSPATH' ) or die( 'Forbidden.' );

class UserDropdownMenu {

    static $add_styles;
    static $add_scripts;
    static function init() {
        add_action('init', array(__CLASS__, 'add_menu_location'));
        add_action('init', array(__CLASS__, 'register_styles'));
        add_action('init', array(__CLASS__, 'register_scripts'));
        add_action('wp_enqueue_styles', array(__CLASS__, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));

        add_action( 'customize_register', array(__CLASS__, 'sagaio_udm_customize_register' ));
        add_shortcode('user_dropdown_menu', array(__CLASS__, 'handle_shortcode'));
    }

    static function add_menu_location() {
        register_nav_menus( array(
            'user_dropdown_menu' => 'User Dropdown Menu'
            ) );
    }

    static function sagaio_udm_customize_register( $wp_customize ) {

        /* Add panel for plugin settings */
        $wp_customize->add_panel( 'sagaio_udm' , array(
          'title' => __( 'User Dropdown Menu', 'sagaio-udm' ),
          'description' => __( 'Settings for SAGAIO User Dropdown Menu', 'sagaio-udm' ),
          'priority' => 90, // Before Navigation.
        ) );

        /* Add section for icon */
        $wp_customize->add_section( 'sagaio_udm_icon' , array(
          'title' => __( 'Icon settings', 'sagaio-udm' ),
          'description' => __( 'Settings for the icon', 'sagaio-udm' ),
          'priority' => 10, // Before Navigation.
          'panel' => 'sagaio_udm',
        ) );

        /* Add section for menu container */
        $wp_customize->add_section( 'sagaio_udm_menu_container' , array(
          'title' => __( 'Menu settings', 'sagaio-udm' ),
          'description' => __( 'Settings for the menu container and its items', 'sagaio-udm' ),
          'priority' => 20, // Before Navigation.
          'panel' => 'sagaio_udm',
        ) );

        /* Add section for login form */
        $wp_customize->add_section( 'sagaio_udm_login_form' , array(
          'title' => __( 'Login form settings', 'sagaio-udm' ),
          'description' => __( 'Settings for the login form', 'sagaio-udm' ),
          'priority' => 30, // Before Navigation.
          'panel' => 'sagaio_udm',
        ) );


        /* Icon: image settings */
        $icon_image = [];

        $icon_image[] = array( 'slug'=>'sagaio_udm_icon_image', 'default' => plugins_url('assets/user_icon_1.png', __FILE__ ), 'label' => __( 'Upload icon image', 'sagaio-udm' ) );

        foreach($icon_image as $image)
        {
            $wp_customize->add_setting( $image['slug'], array( 'default' => $image['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $image['slug'], array( 'label' => $image['label'], 'section' => 'sagaio_udm_icon', 'settings' => 'sagaio_udm_icon_image')));
        }

        /* Icon: height, widths, margins and paddings */
        $icon_hwmp = [];

        $icon_hwmp[] = array( 'slug'=>'sagaio_udm_icon_width', 'default' => '30', 'label' => __( 'Icon width', 'sagaio-udm' ) );
        $icon_hwmp[] = array( 'slug'=>'sagaio_udm_icon_height', 'default' => '30', 'label' => __( 'Icon height', 'sagaio-udm' ) );
        $icon_hwmp[] = array( 'slug'=>'sagaio_udm_icon_margin', 'default' => '0', 'label' => __( 'Icon margin', 'sagaio-udm' ) );

        foreach($icon_hwmp as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'number', 'label' => $setting['label'], 'section' => 'sagaio_udm_icon', 'input_attrs' => array( 'min' => 0, 'max' => 100) ));
        }

        /* Icon: position */
        $icon_positions = [];

        $icon_positions[] = array( 'slug'=>'sagaio_udm_icon_outer_wrapper_position', 'default' => 'relative', 'label' => __( 'Outer wrapper position', 'sagaio-udm' ) );

        foreach($icon_positions as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'select', 'label' => $setting['label'], 'section' => 'sagaio_udm_icon', 'choices' => array( 'relative' => 'Relative', 'absolute' => 'Absolute', 'fixed' => 'Fixed', 'static' => 'Static' ) ));
        }


        /* Login form: border */
        $login_form_border[] = array( 'slug'=>'sagaio_udm_login_button_border_width', 'default' => '1', 'label' => __( 'Login button border width', 'sagaio-udm' ) );

        foreach($login_form_border as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'number', 'label' => $setting['label'], 'section' => 'sagaio_udm_login_form', 'input_attrs' => array( 'min' => 0, 'max' => 100) ));
        }

        /* Login form: border style */
        $login_form_borderstyle = [];

        $login_form_borderstyle[] = array( 'slug'=>'sagaio_udm_login_input_border_style', 'default' => 'solid', 'label' => __( 'Login fields border style', 'sagaio-udm' ) );
        $login_form_borderstyle[] = array( 'slug'=>'sagaio_udm_login_button_border_style', 'default' => 'solid', 'label' => __( 'Login button border style', 'sagaio-udm' ) );

        foreach($login_form_borderstyle as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'select', 'label' => $setting['label'], 'section' => 'sagaio_udm_login_form', 'choices' => array( 'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed', 'double' => 'Dotted', 'groove' => 'Groove', 'ridge' => 'Ridge') ));
        }


        /* Login form: show and hide */
        $login_form_radios[] = array( 'slug'=>'sagaio_udm_display_login', 'default' => true, 'label' => __( 'Display login form?', 'sagaio-udm' ) );
        $login_form_radios[] = array( 'slug'=>'sagaio_udm_display_login_header', 'default' => true, 'label' => __( 'Display login form header?', 'sagaio-udm' ) );
        $login_form_radios[] = array( 'slug'=>'sagaio_udm_display_login_remember', 'default' => true, 'label' => __( 'Display login form remember checkbox?', 'sagaio-udm' ) );
        $login_form_radios[] = array( 'slug'=>'sagaio_udm_display_login_labels', 'default' => false, 'label' => __( 'Display login form labels?', 'sagaio-udm' ) );
        $login_form_radios[] = array( 'slug'=>'sagaio_udm_display_login_placeholders', 'default' => true, 'label' => __( 'Display login form placeholders?', 'sagaio-udm' ) );

        foreach($login_form_radios as $login_form_radio)
        {
            $wp_customize->add_setting( $login_form_radio['slug'], array( 'default' => $login_form_radio['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $login_form_radio['slug'], array( 'type' => 'radio', 'label' => $login_form_radio['label'], 'section' => 'sagaio_udm_login_form', 'choices' => array( true => __('Yes', 'sagaio-udm'), false => __('No', 'sagaio-udm') ) ));
        }


        /* Login form: alignments */
        $login_form_alignments = [];

        $login_form_alignments[] = array( 'slug'=>'sagaio_udm_login_header_alignment', 'default' => 'left', 'label' => __( 'Align header left/center/right', 'sagaio-udm' ) );
        $login_form_alignments[] = array( 'slug'=>'sagaio_udm_login_button_alignment', 'default' => 'left', 'label' => __( 'Align button left/center/right', 'sagaio-udm' ) );

        foreach($login_form_alignments as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'select', 'label' => $setting['label'], 'section' => 'sagaio_udm_login_form', 'choices' => array( 'left' => 'Left', 'center' => 'Center', 'right' => 'Right') ));
        }


        /* Login form: text fields */
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_header', 'default' => 'Login', 'label' => __( 'Label for the header above the fields', 'sagaio-udm' ), 'description' => 'Default is Login' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_header_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Header font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_header_font_style', 'default' => 'normal', 'label' => __( 'Header font style', 'sagaio-udm' ), 'description' => 'Default is normal' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_username', 'default' => 'Username', 'label' => __( 'Label for Username', 'sagaio-udm' ), 'description' => 'Default is Username' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_password', 'default' => 'Password', 'label' => __( 'Label for Password', 'sagaio-udm' ), 'description' => 'Default is Password' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_username_placeholder', 'default' => 'Username', 'label' => __( 'Placeholder for Username', 'sagaio-udm' ), 'description' => 'Default is Username' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_password_placeholder', 'default' => 'Password', 'label' => __( 'Placeholder for Password', 'sagaio-udm' ), 'description' => 'Default is Password' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_remember', 'default' => 'Remember Me', 'label' => __( 'Label for Remember', 'sagaio-udm' ), 'description' => 'Default is Remember Me' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_text_login_button', 'default' => 'Login', 'label' => __( 'Label for Login button', 'sagaio-udm' ), 'description' => 'Default is Login' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_input_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Input font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_input_font_style', 'default' => 'bold', 'label' => __( 'Input font style', 'sagaio-udm' ), 'description' => 'Default is bold' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_label_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Label font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_button_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Button font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $login_form_settings[] = array( 'slug'=>'sagaio_udm_login_button_font_style', 'default' => 'normal', 'label' => __( 'Button font style', 'sagaio-udm' ), 'description' => 'Default is normal' );

        foreach($login_form_settings as $login_form_setting)
        {
            $wp_customize->add_setting( $login_form_setting['slug'], array( 'default' => $login_form_setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $login_form_setting['slug'], array( 'type' => 'input', 'label' => $login_form_setting['label'], 'description' => $login_form_setting['description'], 'section' => 'sagaio_udm_login_form' ));
        }

        /* Login form: color settings */
        $login_form_colors = [];

        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_header_color', 'default' => '#1b1b1b', 'label' => __( 'Login header color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_label_color', 'default' => '#1b1b1b', 'label' => __( 'Text label color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_input_background_color', 'default' => '#f1f1f1', 'label' => __( 'Input background color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_input_border_color', 'default' => '#f1f1f1', 'label' => __( 'Input border color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_input_background_color_focus', 'default' => '#f1f1f1', 'label' => __( 'Input background color on focus', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_input_color', 'default' => '#1b1b1b', 'label' => __( 'Input color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_input_color_focus', 'default' => '#1b1b1b', 'label' => __( 'Input color on focus', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_border_color', 'default' => '#1b1b1b', 'label' => __( 'Button border color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_border_color_hover', 'default' => '#1b1b1b', 'label' => __( 'Button border color hover', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_background_color', 'default' => '#ffffff', 'label' => __( 'Button background color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_background_color_hover', 'default' => '#1b1b1b', 'label' => __( 'Button background hover color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_color', 'default' => '#1b1b1b', 'label' => __( 'Button text color', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_color_active', 'default' => '#ffffff', 'label' => __( 'Button text color on hover', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_background_color_active', 'default' => '#1b1b1b', 'label' => __( 'Button background color on click', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_color_active', 'default' => '#ffffff', 'label' => __( 'Button text color on click', 'sagaio-udm' ) );
        $login_form_colors[] = array( 'slug'=>'sagaio_udm_login_button_color_hover', 'default' => '#ffffff', 'label' => __( 'Button text color on hover', 'sagaio-udm' ) );

        foreach($login_form_colors as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting['slug'], array( 'label' => $setting['label'], 'section' => 'sagaio_udm_login_form', 'settings' => $setting['slug'] )));
        }

        /* Login form: height, widths, margins and paddings */
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_font_size', 'default' => '20', 'label' => __( 'Login header font size', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_line_height', 'default' => '24', 'label' => __( 'Login header line height', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_margin_top', 'default' => '0', 'label' => __( 'Login header margin-top', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_margin_right', 'default' => '0', 'label' => __( 'Login header margin-right', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_margin_bottom', 'default' => '0', 'label' => __( 'Login header margin-bottom', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_header_margin_left', 'default' => '0', 'label' => __( 'Login header margin-left', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_fields_margin_top', 'default' => '0', 'label' => __( 'Input fields margin-top', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_fields_margin_right', 'default' => '0', 'label' => __( 'Input fields margin-right', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_fields_margin_bottom', 'default' => '0', 'label' => __( 'Input fields margin-bottom', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_fields_margin_left', 'default' => '0', 'label' => __( 'Input fields margin-left', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_width', 'default' => '200', 'label' => __( 'Input fields width', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_border_width', 'default' => '1', 'label' => __( 'Input fields border width', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_border_radius', 'default' => '4', 'label' => __( 'Input fields border radius', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_padding_left_right', 'default' => '15', 'label' => __( 'Input fields padding left/right', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_padding_top_bottom', 'default' => '10', 'label' => __( 'Input fields padding top/bottom', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_font_size', 'default' => '20', 'label' => __( 'Input fields font size', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_input_line_height', 'default' => '24', 'label' => __( 'Input fields line height', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_label_font_size', 'default' => '20', 'label' => __( 'Input label font size', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_label_line_height', 'default' => '24', 'label' => __( 'Input label line height', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_font_size', 'default' => '20', 'label' => __( 'Button font size', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_border_width', 'default' => '1', 'label' => __( 'Button border width', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_border_radius', 'default' => '4', 'label' => __( 'Button border radius', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_padding_top', 'default' => '5', 'label' => __( 'Button padding-top', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_padding_right', 'default' => '10', 'label' => __( 'Button padding-right', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_padding_bottom', 'default' => '5', 'label' => __( 'Button padding-bottom', 'sagaio-udm' ) );
        $login_form_hwmp[] = array( 'slug'=>'sagaio_udm_login_button_padding_left', 'default' => '10', 'label' => __( 'Button padding-left', 'sagaio-udm' ) );

        foreach($login_form_hwmp as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'number', 'label' => $setting['label'], 'section' => 'sagaio_udm_login_form', 'input_attrs' => array( 'min' => 0, 'max' => 100) ));
        }


        /* Menu container: show and hide */
        $menu_radios = [];

        $menu_radios[] = array( 'slug'=>'sagaio_udm_display_user', 'default' => true, 'label' => __( 'Display current user name & lastname?', 'sagaio-udm' ) );
        $menu_radios[] = array( 'slug'=>'sagaio_udm_display_user_header', 'default' => true, 'label' => __( 'Display header above user?', 'sagaio-udm' ) );
        $menu_radios[] = array( 'slug'=>'sagaio_udm_display_logout', 'default' => true, 'label' => __( 'Display logout link?', 'sagaio-udm' ) );

        foreach($menu_radios as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'radio', 'label' => $setting['label'], 'section' => 'sagaio_udm_menu_container', 'choices' => array( true => __('Yes', 'sagaio-udm'), false => __('No', 'sagaio-udm') ) ));
        }

        /* Menu container: text fields */
        $menu_text = [];

        $menu_text[] = array( 'slug'=>'sagaio_udm_user_header', 'default' => 'User', 'label' => __( 'Header text', 'sagaio-udm' ), 'description' => '' );
        $menu_text[] = array( 'slug'=>'sagaio_udm_user_header_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Header font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $menu_text[] = array( 'slug'=>'sagaio_udm_user_header_font_style', 'default' => 'bold', 'label' => __( 'Header font style', 'sagaio-udm' ), 'description' => 'Default is bold' );
        $menu_text[] = array( 'slug'=>'sagaio_udm_user_font_family', 'default' => 'Arial, sans-serif', 'label' => __( 'Usertext font family', 'sagaio-udm' ), 'description' => 'Default is Arial, sans-serif' );
        $menu_text[] = array( 'slug'=>'sagaio_udm_user_font_style', 'default' => 'normal', 'label' => __( 'Usertext font style', 'sagaio-udm' ), 'description' => 'Default is normal' );
        $menu_text[] = array( 'slug'=>'sagaio_udm_logout_text', 'default' => 'Logout', 'label' => __( 'Logout text', 'sagaio-udm' ), 'description' => '' );

        foreach($menu_text as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'input', 'label' => $setting['label'], 'description' => $setting['description'], 'section' => 'sagaio_udm_menu_container' ));
        }

        /* Menu container: color settings */
        $menu_colors = [];

        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_background_color', 'default' => '#ffffff', 'label' => __( 'Menu background color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_text_color', 'default' => '#1b1b1b', 'label' => __( 'Menu text color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_text_hover_color', 'default' => '#000000', 'label' => __( 'Menu text hover color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_item_background_color', 'default' => '#ffffff', 'label' => __( 'Menu items background color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_item_background_hover_color', 'default' => '#f1f1f1', 'label' => __( 'Menu items background hover color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_border_color', 'default' => '#f1f1f1', 'label' => __( 'Menu border color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_item_border_color', 'default' => '#f1f1f1', 'label' => __( 'Menu item border color', 'sagaio-udm' ) );
        $menu_colors[] = array( 'slug'=>'sagaio_udm_menu_indicator_color', 'default' => '#f1f1f1', 'label' => __( 'Menu indicator color', 'sagaio-udm' ) );

        foreach($menu_colors as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $setting['slug'], array( 'label' => $setting['label'], 'section' => 'sagaio_udm_menu_container', 'settings' => $setting['slug'] )));
        }

        /* Menu container: border style */
        $menu_borderstyle = [];

        $menu_borderstyle[] = array( 'slug'=>'sagaio_udm_menu_border_style', 'default' => 'solid', 'label' => __( 'Menu border style', 'sagaio-udm' ) );
        $menu_borderstyle[] = array( 'slug'=>'sagaio_udm_menu_item_border_style', 'default' => 'solid', 'label' => __( 'Menu item border style', 'sagaio-udm' ) );

        foreach($menu_borderstyle as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'select', 'label' => $setting['label'], 'section' => 'sagaio_udm_menu_container', 'choices' => array( 'solid' => 'Solid', 'dotted' => 'Dotted', 'dashed' => 'Dashed', 'double' => 'Dotted', 'groove' => 'Groove', 'ridge' => 'Ridge') ));
        }

        /* Menu container: height, widths, margins and paddings */
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_border_width', 'default' => '2', 'label' => __( 'Menu border width', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_item_border_width', 'default' => '2', 'label' => __( 'Menu item border bottom width', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_margin_top', 'default' => '15', 'label' => __( 'Menu top margin', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_padding_top', 'default' => '10', 'label' => __( 'Menu padding top', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_padding_right', 'default' => '10', 'label' => __( 'Menu padding right', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_padding_bottom', 'default' => '10', 'label' => __( 'Menu padding bottom', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_padding_left', 'default' => '10', 'label' => __( 'Menu padding left', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_border_radius', 'default' => '4', 'label' => __( 'Menu border radius', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_item_padding_left_right', 'default' => '15', 'label' => __( 'Menu item padding left/right', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_item_padding_top_bottom', 'default' => '10', 'label' => __( 'Menu item padding top/bottom', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_item_font_size', 'default' => '24', 'label' => __( 'Menu item font size', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_menu_item_line_height', 'default' => '28', 'label' => __( 'Menu item line height', 'sagaio-udm' ) );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_user_header_font_size', 'default' => '12', 'label' => __( 'Header font size', 'sagaio-udm' ), 'description' => 'Default is 12px' );
        $menu_hwmp[] = array( 'slug'=>'sagaio_udm_user_font_size', 'default' => '14', 'label' => __( 'Usertext font size', 'sagaio-udm' ), 'description' => 'Default is 14px' );

        foreach($menu_hwmp as $setting)
        {
            $wp_customize->add_setting( $setting['slug'], array( 'default' => $setting['default'], 'type' => 'option', 'capability' => 'edit_theme_options' ));
            $wp_customize->add_control( $setting['slug'], array( 'type' => 'number', 'label' => $setting['label'], 'section' => 'sagaio_udm_menu_container', 'input_attrs' => array( 'min' => 0, 'max' => 100) ));
        }

    }

    static function echo_customizer_styles() {
        $sagaio_udm_icon_image = get_option('sagaio_udm_icon_image', plugins_url('assets/user_icon_1.png', __FILE__ ));
        $sagaio_udm_icon_width = get_option('sagaio_udm_icon_width', '30');
        $sagaio_udm_icon_height = get_option('sagaio_udm_icon_height', '30');
        $sagaio_udm_icon_margin = get_option('sagaio_udm_icon_margin', '0');

        $sagaio_udm_menu_background_color = get_option('sagaio_udm_menu_background_color', '#ffffff');
        $sagaio_udm_menu_text_color = get_option('sagaio_udm_menu_text_color', '#1b1b1b');
        $sagaio_udm_menu_text_hover_color = get_option('sagaio_udm_menu_text_hover_color', '#000000');
        $sagaio_udm_menu_item_background_color = get_option('sagaio_udm_menu_item_background_color', '#ffffff');
        $sagaio_udm_menu_item_background_hover_color = get_option('sagaio_udm_menu_item_background_hover_color', '#f1f1f1');
        $sagaio_udm_menu_border_color = get_option('sagaio_udm_menu_border_color', '#f1f1f1');
        $sagaio_udm_menu_indicator_color = get_option('sagaio_udm_menu_indicator_color', '#f1f1f1');

        $sagaio_udm_menu_margin_top = get_option('sagaio_udm_menu_margin_top', '15');
        $sagaio_udm_menu_padding_top = get_option('sagaio_udm_menu_padding_top', '10');
        $sagaio_udm_menu_padding_right = get_option('sagaio_udm_menu_padding_right', '10');
        $sagaio_udm_menu_padding_bottom = get_option('sagaio_udm_menu_padding_bottom', '10');
        $sagaio_udm_menu_padding_left = get_option('sagaio_udm_menu_padding_left', '10');
        $sagaio_udm_menu_border_width = get_option('sagaio_udm_menu_border_width', '2');
        $sagaio_udm_menu_border_radius = get_option('sagaio_udm_menu_border_radius', '4');
        $sagaio_udm_menu_border_style = get_option('sagaio_udm_menu_border_style', 'solid');

        $sagaio_udm_menu_item_border_width = get_option('sagaio_udm_menu_item_border_width', '2');
        $sagaio_udm_menu_item_border_color = get_option('sagaio_udm_menu_item_border_color', '#f1f1f1');
        $sagaio_udm_menu_item_border_style = get_option('sagaio_udm_menu_item_border_style', 'solid');
        $sagaio_udm_menu_item_padding_left_right = get_option('sagaio_udm_menu_item_padding_left_right', '15');
        $sagaio_udm_menu_item_padding_top_bottom = get_option('sagaio_udm_menu_item_padding_top_bottom', '10');
        $sagaio_udm_menu_item_font_size = get_option('sagaio_udm_menu_item_font_size', '24');
        $sagaio_udm_menu_item_line_height = get_option('sagaio_udm_menu_item_line_height', '28');

        $sagaio_udm_user_header_font_family = get_option('sagaio_udm_user_header_font_family', 'Arial, sans-serif');
        $sagaio_udm_user_header_font_style = get_option('sagaio_udm_user_header_font_style', 'bold');
        $sagaio_udm_user_header_font_size = get_option('sagaio_udm_user_header_font_size', '12');
        $sagaio_udm_user_font_family = get_option('sagaio_udm_user_font_family', 'Arial, sans-serif');
        $sagaio_udm_user_font_style = get_option('sagaio_udm_user_font_style', 'normal');
        $sagaio_udm_user_font_size = get_option('sagaio_udm_user_font_size', '14');

        $sagaio_udm_login_input_font_family = get_option('sagaio_udm_login_input_font_family', 'Arial, sans-serif');
        $sagaio_udm_login_input_font_style = get_option('sagaio_udm_login_input_font_style', 'bold');
        $sagaio_udm_login_input_font_size = get_option('sagaio_udm_login_input_font_size', '24');
        $sagaio_udm_login_input_width = get_option('sagaio_udm_login_input_width', '200');
        $sagaio_udm_login_input_line_height = get_option('sagaio_udm_login_input_line_height', '28');
        $sagaio_udm_login_input_padding_top_bottom = get_option('sagaio_udm_login_input_padding_top_bottom', '10');
        $sagaio_udm_login_input_padding_left_right = get_option('sagaio_udm_login_input_padding_left_right', '15');
        $sagaio_udm_login_input_border_radius = get_option('sagaio_udm_login_input_border_radius', '4');
        $sagaio_udm_login_input_border_width = get_option('sagaio_udm_login_input_border_width', '1');
        $sagaio_udm_login_input_border_style = get_option('sagaio_udm_login_input_border_style', 'solid');
        $sagaio_udm_login_input_border_color = get_option('sagaio_udm_login_input_border_color', '#f1f1f1');
        $sagaio_udm_login_input_background_color = get_option('sagaio_udm_login_input_background_color', '#f1f1f1');
        $sagaio_udm_login_input_background_color_focus = get_option('sagaio_udm_login_input_background_color_focus', '#f1f1f1');
        $sagaio_udm_login_input_color_focus = get_option('sagaio_udm_login_input_color_focus', '#1b1b1b');
        $sagaio_udm_login_input_color = get_option('sagaio_udm_login_input_color', '#1b1b1b');

        $sagaio_udm_login_label_font_family = get_option('sagaio_udm_login_label_font_family', 'Arial, sans-serif');
        $sagaio_udm_login_label_font_style = get_option('sagaio_udm_login_label_font_style', 'bold');
        $sagaio_udm_login_label_font_size = get_option('sagaio_udm_login_label_font_size', '20');
        $sagaio_udm_login_label_color = get_option('sagaio_udm_login_label_color', '#1b1b1b');
        $sagaio_udm_login_label_line_height = get_option('sagaio_udm_login_label_line_height', '24');

        $sagaio_udm_login_button_font_family = get_option('sagaio_udm_login_button_font_family', 'Arial, sans-serif');
        $sagaio_udm_login_button_font_style = get_option('sagaio_udm_login_button_font_style', 'normal');
        $sagaio_udm_login_button_font_size = get_option('sagaio_udm_login_button_font_size', '20');
        $sagaio_udm_login_button_background_color = get_option('sagaio_udm_login_button_background_color', '#1b1b1b');
        $sagaio_udm_login_button_color = get_option('sagaio_udm_login_button_color', '#ffffff');
        $sagaio_udm_login_button_background_color_hover = get_option('sagaio_udm_login_button_background_color_hover', '#1b1b1b');
        $sagaio_udm_login_button_color_hover = get_option('sagaio_udm_login_button_color_hover', '#ffffff');
        $sagaio_udm_login_button_background_color_active = get_option('sagaio_udm_login_button_background_color_active', '#1b1b1b');
        $sagaio_udm_login_button_color_active = get_option('sagaio_udm_login_button_color_active', '#ffffff');
        $sagaio_udm_login_button_border_color = get_option('sagaio_udm_login_button_border_color', '#1b1b1b');
        $sagaio_udm_login_button_border_color_hover = get_option('sagaio_udm_login_button_border_color_hover', '#1b1b1b');
        $sagaio_udm_login_button_border_radius = get_option('sagaio_udm_login_button_border_radius', '4');
        $sagaio_udm_login_button_border_width = get_option('sagaio_udm_login_button_border_width', '1');
        $sagaio_udm_login_button_border_style = get_option('sagaio_udm_login_button_border_style', 'solid');
        $sagaio_udm_login_button_padding_top = get_option('sagaio_udm_login_button_padding_top', '5');
        $sagaio_udm_login_button_padding_right = get_option('sagaio_udm_login_button_padding_right', '10');
        $sagaio_udm_login_button_padding_bottom = get_option('sagaio_udm_login_button_padding_bottom', '5');
        $sagaio_udm_login_button_padding_left = get_option('sagaio_udm_login_button_padding_left', '10');

        $sagaio_udm_login_fields_margin_top = get_option('sagaio_udm_login_fields_margin_top', '0');
        $sagaio_udm_login_fields_margin_right = get_option('sagaio_udm_login_fields_margin_right', '0');
        $sagaio_udm_login_fields_margin_bottom = get_option('sagaio_udm_login_fields_margin_bottom', '0');
        $sagaio_udm_login_fields_margin_left = get_option('sagaio_udm_login_fields_margin_left', '0');

        $sagaio_udm_login_header_margin_top = get_option('sagaio_udm_login_header_margin_top', '0');
        $sagaio_udm_login_header_margin_right = get_option('sagaio_udm_login_header_margin_right', '0');
        $sagaio_udm_login_header_margin_bottom = get_option('sagaio_udm_login_header_margin_bottom', '0');
        $sagaio_udm_login_header_margin_left = get_option('sagaio_udm_login_header_margin_left', '0');
        $sagaio_udm_login_header_font_family = get_option('sagaio_udm_login_header_font_family', 'Arial, sans-serif');
        $sagaio_udm_login_header_font_style = get_option('sagaio_udm_login_header_font_style', 'normal');
        $sagaio_udm_login_header_font_size = get_option('sagaio_udm_login_header_font_size', '20');
        $sagaio_udm_login_header_color = get_option('sagaio_udm_login_header_color', '#1b1b1b');
        $sagaio_udm_login_header_line_height = get_option('sagaio_udm_login_header_line_height', '24');

        // Alignments
        $sagaio_udm_login_header_alignment = get_option('sagaio_udm_login_header_alignment', 'left');
        $sagaio_udm_login_button_alignment = get_option('sagaio_udm_login_button_alignment', 'left');

        // Outer Wrapper Position
        $sagaio_udm_icon_outer_wrapper_position = get_option('sagaio_udm_icon_outer_wrapper_position', 'relative');

        // Determine if we whould display labels for the input fields
        $display_login_labels = get_option('sagaio_udm_display_login_labels', false);
        if(!$display_login_labels) {
            $label_display_attr = 'none';
        } else {
            $label_display_attr = 'inline-block';
        }

        $style = '<style>';
        $style .= '#sagaio-udm-wrapper {
            position: '.$sagaio_udm_icon_outer_wrapper_position.' !important;
        }';
        $style .= '.sagaio-udm-menu {
            background: '.$sagaio_udm_menu_background_color.' !important;
            color: '.$sagaio_udm_menu_text_color.' !important;
            border: '.$sagaio_udm_menu_border_width.'px '.$sagaio_udm_menu_border_style.' '.$sagaio_udm_menu_border_color.' !important;
            margin-top: '.$sagaio_udm_menu_margin_top.'px !important;
            list-style: none !important;
            padding: '.$sagaio_udm_menu_padding_top.'px '.$sagaio_udm_menu_padding_right.'px '.$sagaio_udm_menu_padding_bottom.'px '.$sagaio_udm_menu_padding_left.'px !important;
            border-radius: '.$sagaio_udm_menu_border_radius.'px !important;
        }';
        $style .= '.sagaio-udm-menu-item {
            background: '.$sagaio_udm_menu_item_background_color.';
            color: '.$sagaio_udm_menu_text_color.' !important;
            padding: '.$sagaio_udm_menu_item_padding_top_bottom.'px '.$sagaio_udm_menu_item_padding_left_right.'px;
            display: block;
            font-size: '.$sagaio_udm_menu_item_font_size.'px;
            line-height: '.$sagaio_udm_menu_item_line_height.'px;
        }';
        $style .= '.sagaio-udm-menu-item:first-child {
            border-top-left-radius: '.$sagaio_udm_menu_border_radius.'px;
            border-top-right-radius: '.$sagaio_udm_menu_border_radius.'px;
            border-bottom: '.$sagaio_udm_menu_item_border_width.'px '.$sagaio_udm_menu_item_border_style.' '.$sagaio_udm_menu_item_border_color.' !important;
        }';
        $style .= '.sagaio-udm-menu-item:last-child {
            border-bottom-left-radius: '.$sagaio_udm_menu_border_radius.'px;
            border-bottom-right-radius: '.$sagaio_udm_menu_border_radius.'px;
            border-bottom: none !important;
        }';
        $style .= '.sagaio-udm-menu-item:hover {
            background-color: '.$sagaio_udm_menu_item_background_hover_color.';
            color: '.$sagaio_udm_menu_text_hover_color.';
        }';
        $style .= '.sagaio-udm-icon {
            height: '.$sagaio_udm_icon_height.'px;
            width: '.$sagaio_udm_icon_width.'px;
            margin: '.$sagaio_udm_icon_margin.'px;
            background-image: url('.$sagaio_udm_icon_image.');
            background-size: contain;
            background-repeat: no-repeat;
        }';
        $style .= '.sagaio-udm-menu-user-header {
            font-family: '.$sagaio_udm_user_header_font_family.';
            font-weight: '.$sagaio_udm_user_header_font_style.';
            font-size: '.$sagaio_udm_user_header_font_size.'px;
        }';
        $style .= '.sagaio-udm-menu-user {
            font-family: '.$sagaio_udm_user_font_family.';
            font-weight: '.$sagaio_udm_user_font_style.';
            font-size: '.$sagaio_udm_user_font_size.'px;
        }';

        if($sagaio_udm_login_header_alignment == 'left') {
            $style .= '.sagaio-udm-login-header {
                text-align: left;
            }';
        }
        if($sagaio_udm_login_header_alignment == 'center') {
            $style .= '.sagaio-udm-login-header {
                text-align: center;
            }';
        }
        if($sagaio_udm_login_header_alignment == 'right') {
            $style .= '.sagaio-udm-login-header {
                text-align: right;
            }';
        }

        $style .= '.sagaio-udm-login-header {
            font-family: '.$sagaio_udm_login_header_font_family.';
            font-weight: '.$sagaio_udm_login_header_font_style.';
            font-size: '.$sagaio_udm_login_header_font_size.'px;
            color: '.$sagaio_udm_login_header_color.';
            line-height:  '.$sagaio_udm_login_header_line_height.'px;
            margin: '.$sagaio_udm_login_header_margin_top.'px '.$sagaio_udm_login_header_margin_right.'px '.$sagaio_udm_login_header_margin_bottom.'px '.$sagaio_udm_login_header_margin_left.'px ;
        }';
        $style .= '#sagaio-udm-login-form input[type="text"], #sagaio-udm-login-form input[type="password"] {
            font-family: '.$sagaio_udm_login_input_font_family.';
            font-weight: '.$sagaio_udm_login_input_font_style.';
            font-size: '.$sagaio_udm_login_input_font_size.'px;
            background-color: '.$sagaio_udm_login_input_background_color.';
            color: '.$sagaio_udm_login_input_color.';
            line-height:  '.$sagaio_udm_login_input_line_height.'px;
            padding: '.$sagaio_udm_login_input_padding_top_bottom.'px '.$sagaio_udm_login_input_padding_left_right.'px;
            border-radius: '.$sagaio_udm_login_input_border_radius.'px;
            border: '.$sagaio_udm_login_input_border_width.'px '.$sagaio_udm_login_input_border_style.' '.$sagaio_udm_login_input_border_color.';
            width: '.$sagaio_udm_login_input_width.'px;
        }';
        $style .= '#sagaio-udm-login-form p {
            margin: '.$sagaio_udm_login_fields_margin_top.'px '.$sagaio_udm_login_fields_margin_right.'px '.$sagaio_udm_login_fields_margin_bottom.'px '.$sagaio_udm_login_fields_margin_left.'px !important;
        }';
        $style .= '#sagaio-udm-login-form input[type="text"]:focus, #sagaio-udm-login-form input[type="password"]:focus {
            background-color: '.$sagaio_udm_login_input_background_color_focus.';
            color: '.$sagaio_udm_login_input_color_focus.';
        }';
        $style .= '#sagaio-udm-login-form label {
            display: '.$label_display_attr.' !important;
            font-family: '.$sagaio_udm_login_label_font_family.';
            font-weight: '.$sagaio_udm_login_label_font_style.';
            font-size: '.$sagaio_udm_login_label_font_size.'px;
            color: '.$sagaio_udm_login_label_color.';
            line-height:  '.$sagaio_udm_login_label_line_height.'px;
        }';

        if($sagaio_udm_login_button_alignment === 'center') {
            $style .= '#sagaio-udm-login-button {
                display: block;
                margin: 0 auto;
            }';
        }
        if($sagaio_udm_login_button_alignment === 'right') {
            $style .= '#sagaio-udm-login-button {
                float: right;
            }';
        }

        $style .= '#sagaio-udm-login-button {
            font-family: '.$sagaio_udm_login_button_font_family.';
            font-weight: '.$sagaio_udm_login_button_font_style.';
            font-size: '.$sagaio_udm_login_button_font_size.'px;
            background-color: '.$sagaio_udm_login_button_background_color.';
            color: '.$sagaio_udm_login_button_color.';
            text-transform: none;
            border-radius: '.$sagaio_udm_login_button_border_radius.'px;
            border: '.$sagaio_udm_login_button_border_width.'px '.$sagaio_udm_login_button_border_style.' '.$sagaio_udm_login_button_border_color.';
            padding: '.$sagaio_udm_login_button_padding_top.'px '.$sagaio_udm_login_button_padding_right.'px '.$sagaio_udm_login_button_padding_bottom.'px '.$sagaio_udm_login_button_padding_left.'px !important;
        }';
        $style .= '#sagaio-udm-login-button:hover {
            background-color: '.$sagaio_udm_login_button_background_color_hover.';
            color: '.$sagaio_udm_login_button_color_hover.';
            border: '.$sagaio_udm_login_button_border_width.'px '.$sagaio_udm_login_button_border_style.' '.$sagaio_udm_login_button_border_color_hover.';
        }';
        $style .= '#sagaio-udm-login-button:active {
            background-color: '.$sagaio_udm_login_button_background_color_active.';
            color: '.$sagaio_udm_login_button_color_active.';
        }';
        $style .= '</style>';

        echo $style;

    }

    static function echo_placeholder_script() {

        $sagaio_udm_login_text_username_placeholder = get_option('sagaio_udm_login_text_username_placeholder', 'Username');
        $sagaio_udm_login_text_password_placeholder = get_option('sagaio_udm_login_text_password_placeholder', 'Password');

        $script = '<script type="text/javascript">';
        $script .= '(function($) { $("#sagaio-udm-login-username").attr("placeholder", "'.$sagaio_udm_login_text_username_placeholder.'"); $("#sagaio-udm-login-password").attr("placeholder", "'.$sagaio_udm_login_text_password_placeholder.'") })(jQuery);';
        $script .= '</script>';

        echo $script;

    }

    static function handle_shortcode($atts) {
        self::$add_styles = true;
        self::$add_scripts = true;

        $atts = shortcode_atts( array(), $atts );

        $holder = 'user_dropdown_menu';

        if( ($locations = get_nav_menu_locations()) && (isset($locations[$holder]))) {

            $menu = get_term( $locations[$holder], 'nav_menu' );

            $menu_items = wp_get_nav_menu_items($menu->term_id);

            $menu_list = '<div id="sagaio-udm-wrapper"><div id="sagaio-udm-inner-wrapper"><div class="dropdown sagaio-udm-icon" data-toggle="dropdown"></div>' . "\n";
            $menu_list .= '<div class="dropdown-menu sagaio-udm-menu">' . "\n";

            // Check if user is logged in
            if( is_user_logged_in() ) {

                $current_user = wp_get_current_user();

                $display_user_header = false;
                $header_text = '';
                $logout_text = '';

                // Determine if we whould display the user in the menu
                $display_user = get_option('sagaio_udm_display_user') ? true : false;
                if($display_user) {

                // Determine if we whould display the user header in the menu
                    if(get_option('sagaio_udm_display_user_header')) {
                        $user_header = empty(get_option('sagaio_udm_user_header')) ? __('User', 'sagaio-udm') : get_option('sagaio_udm_user_header') ;
                        // Add header to the menu
                        $menu_list .= '<div class="sagaio-udm-menu-user-header">'.$user_header.'</div>' ."\n";
                    }
                    // Add user firstname and lastname to the menu
                    if(empty($current_user->user_firstname)) {
                            $menu_list .= '<div class="sagaio-udm-menu-user">'.$current_user->user_login.'</div>' ."\n";
                    } else {
                         $menu_list .= '<div class="sagaio-udm-menu-user">'.$current_user->user_firstname.' '.$current_user->user_lastname.'</div>' ."\n";
                    }
                }

            }

            foreach($menu_items as $menu_item) {

                $link = $menu_item->url;
                $title = $menu_item->title;

                $menu_list .= '<a href="'.$link.'" class="sagaio-udm-menu-item">'.$title.'</a>' ."\n";
            }


            // Check if user is logged in
            if( is_user_logged_in() ) {
                // Determine if we whould display a logout link
                $display_logout = get_option('sagaio_udm_display_logout', true);

                if($display_logout) {
                    $logout_text = get_option('sagaio_udm_logout_text', 'Logout');
                    $menu_list .= '<a href="'. wp_logout_url( get_permalink() ).'" class="sagaio-udm-menu-item">'.$logout_text.'</a>' ."\n";
                }
            }
            if( ! is_user_logged_in() ) {
                // Determine if we whould display a login form
                $display_login = get_option('sagaio_udm_display_login', true);
                // Determine if we whould display the remember me button
                $display_login_remember = get_option('sagaio_udm_display_login_remember', true);

                $login_text_username = get_option('sagaio_udm_login_text_username', 'Username');
                $login_text_password = get_option('sagaio_udm_login_text_password', 'Password');
                $login_text_remember = get_option('sagaio_udm_login_text_remember', 'Remember Me');
                $login_text_login_button = get_option('sagaio_udm_login_text_login_button', 'Login');

                if($display_login) {
                    $args = array(
                        'echo' => false,
                        'redirect' => admin_url(),
                        'form_id' => 'sagaio-udm-login-form',
                        'id_username'    => 'sagaio-udm-login-username',
                        'id_password'    => 'sagaio-udm-login-password',
                        'id_remember'    => 'sagaio-udm-login-rememberme',
                        'id_submit'      => 'sagaio-udm-login-button',
                        'label_username' => $login_text_username,
                        'label_password' => $login_text_password,
                        'label_remember' => $login_text_remember,
                        'label_log_in' => $login_text_login_button,
                        'remember' => $display_login_remember
                    );

                    // important: Restart menu list string concatenation
                    $menu_list = '<div id="sagaio-udm-wrapper"><div id="sagaio-udm-inner-wrapper"><div class="dropdown sagaio-udm-icon" data-toggle="dropdown"></div>' . "\n";
                    $menu_list .= '<div class="dropdown-menu sagaio-udm-menu">' . "\n";
                    if(get_option('sagaio_udm_display_login_header', true)) {
                        $header = get_option('sagaio_udm_login_header', 'Login');
                        $menu_list .= '<div class="sagaio-udm-login-header">'.$header.'</div>'. "\n";
                    }
                    $menu_list .= wp_login_form( $args );
                }

            }

            $menu_list .= '</div>' ."\n";
            $menu_list .= '</div></div></div>' ."\n";

            wp_enqueue_style( 'udm-bootstrap-css');
            wp_enqueue_style( 'udm-style');
            wp_enqueue_script( 'udm-boostrap');

            self::echo_customizer_styles();
            return $menu_list;

            // Optionally echo out scripts for setting the attribute "placeholder" on the login input fields
            $placeholder_enabled = get_option('sagaio_udm_display_login_placeholders', true);
            if( $placeholder_enabled) {
                self::echo_placeholder_script();
            }
        } else {
            return;
        }
    }

    static function register_styles() {
        wp_register_style( 'udm-bootstrap-css', plugins_url( 'css/dropdown.css', __FILE__) );
        wp_register_style( 'udm-style', plugins_url( 'css/user-dropdown-menu.css', __FILE__ ) );
    }

    static function register_scripts() {
        wp_register_script( 'bootstrap-util', plugins_url( 'js/util.js', __FILE__ ), array( 'jquery'), '1.0', true );
        wp_register_script( 'udm-boostrap', plugins_url( 'js/dropdown.js', __FILE__ ), array( 'bootstrap-util'), '1.0', true );
   }

    static function enqueue_styles() {
        if ( ! self::$add_styles )
            return;
        wp_enqueue_styles('udm-bootstrap-css');
        wp_enqueue_styles('udm-style');
    }

    static function enqueue_scripts() {
        if ( ! self::$add_scripts )
            return;
        wp_print_scripts('udm-boostrap');
    }
}
UserDropdownMenu::init();