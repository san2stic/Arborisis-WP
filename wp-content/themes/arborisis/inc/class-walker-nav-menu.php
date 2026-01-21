<?php
/**
 * Custom Navigation Walker for Tailwind CSS
 *
 * @package Arborisis
 */

if (!defined('ABSPATH'))
    exit;

class Arborisis_Walker_Nav_Menu extends Walker_Nav_Menu
{

    /**
     * Starts the list before the elements are added
     */
    public function start_lvl(&$output, $depth = 0, $args = null)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat($t, $depth);

        // Dropdown menu classes for Tailwind
        $classes = 'absolute left-0 mt-2 w-48 bg-white dark:bg-dark-800 rounded-lg shadow-xl border border-dark-200 dark:border-dark-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50';

        $output .= "{$n}{$indent}<ul class=\"$classes\">{$n}";
    }

    /**
     * Starts the element output
     */
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0)
    {
        if (isset($args->item_spacing) && 'discard' === $args->item_spacing) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ($depth) ? str_repeat($t, $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        // Add parent class if has children
        if (in_array('menu-item-has-children', $classes)) {
            $classes[] = 'group relative';
        }

        /**
         * Filters the arguments for a single nav menu item
         */
        $args = apply_filters('nav_menu_item_args', $args, $item, $depth);

        /**
         * Filters the CSS classes applied to a menu item's list item element
         */
        $class_names = implode(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        /**
         * Filters the ID applied to a menu item's list item element
         */
        $id = apply_filters('nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts = array();
        $atts['title'] = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '';
        if ('_blank' === $item->target && empty($item->xfn)) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href'] = !empty($item->url) ? $item->url : '';
        $atts['aria-current'] = $item->current ? 'page' : '';

        // Link classes based on depth
        if ($depth === 0) {
            // Top level menu items
            $link_class = 'nav-link px-4 py-2 rounded-lg hover:bg-dark-100 dark:hover:bg-dark-800 transition-colors';
            if ($item->current) {
                $link_class .= ' active bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-500';
            }
        } else {
            // Dropdown items
            $link_class = 'block px-4 py-2 hover:bg-dark-50 dark:hover:bg-dark-700 transition-colors';
            if ($item->current) {
                $link_class .= ' text-primary-600 dark:text-primary-500';
            }
        }

        $atts['class'] = $link_class;

        /**
         * Filters the HTML attributes applied to a menu item's anchor element
         */
        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (is_scalar($value) && '' !== $value && false !== $value) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters('the_title', $item->title, $item->ID);

        /**
         * Filters a menu item's title
         */
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;

        // Add dropdown arrow if has children
        if (in_array('menu-item-has-children', $classes) && $depth === 0) {
            $item_output .= '<svg class="w-4 h-4 ml-1 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>';
        }

        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filters a menu item's starting output
         */
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
