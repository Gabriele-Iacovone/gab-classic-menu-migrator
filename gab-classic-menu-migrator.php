<?php
/**
 * Plugin Name: Gab - Classic Menu Migrator
 * Description: Import & Export a WordPress Menu between websites - for Classic Themes.
 * Version: 1.5.1
 * Author: Gabriele Iacovone
 */

if (!defined('ABSPATH')) exit;

class Gab_Classic_Menu_Migrator {

    public function __construct() {
        add_action('admin_menu', [$this, 'gab_add_menu_page']);
        add_action('admin_init', [$this, 'gab_handle_export']);
        add_action('admin_init', [$this, 'gab_handle_import']);
    }

    // Aggiunge la pagina nel menu Strumenti
    public function gab_add_menu_page() {
        add_management_page(
            'Classic Menu Migrator',
            'Classic Menu Migrator',
            'manage_options',
            'gab-classic-menu-migrator',
            [$this, 'gab_render_admin_page']
        );
    }

    // Interfaccia Admin
    public function gab_render_admin_page() {
        $menus = wp_get_nav_menus();
        ?>
        <div class="wrap">
            <h1>Classic Menu Migrator</h1>
            <p>Import & Export WordPress Menus between websites</p>
            <hr>
            <div style="display: flex; gap: 40px;">
                <div>
                    <h2>Export</h2>
                    <form method="post">
                        <?php wp_nonce_field('gab_export_action', 'gab_export_nonce'); ?>
                        <select name="gab_menu_id">
                            <?php foreach ($menus as $menu) : ?>
                                <option value="<?php echo $menu->term_id; ?>"><?php echo esc_html($menu->name); ?></option>
                            <?php endforeach; ?>
                        </select><br><br>
                        <input type="submit" name="gab_submit_export" class="button button-primary" value="Download JSON">
                    </form>
                </div>
                <div>
                    <h2>Import</h2>
                    <form method="post" enctype="multipart/form-data">
                        <?php wp_nonce_field('gab_import_action', 'gab_import_nonce'); ?>
                        <input type="file" name="gab_import_file" accept=".json"><br><br>
                        <input type="submit" name="gab_submit_import" class="button button-secondary" value="Import Menu">
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    // Logica di Esportazione
    public function gab_handle_export() {
        if (isset($_POST['gab_submit_export']) && check_admin_referer('gab_export_action', 'gab_export_nonce')) {
            $menu_id = intval($_POST['gab_menu_id']);
            $menu_object = wp_get_nav_menu_object($menu_id);
            $menu_items = wp_get_nav_menu_items($menu_id);

            if (!$menu_items) return;

            $export_data = ['name' => $menu_object->name, 'items' => []];
            $site_url = home_url();

            foreach ($menu_items as $item) {
                // Rende l'URL relativo per il JSON
                $relative_url = str_replace($site_url, '', $item->url);
                if (empty($relative_url) && $item->url == $site_url) $relative_url = '/';

                $export_data['items'][] = [
                    'id'          => $item->ID,
                    'parent'      => $item->menu_item_parent,
                    'title'       => $item->title,
                    'url'         => $relative_url,
                    'object'      => $item->object,
                    'type'        => $item->type,
                    'target'      => $item->target,
                    'classes'     => implode(' ', (array)$item->classes),
                    'description' => $item->description,
                    'xfn'         => $item->xfn
                ];
            }

            $filename = 'menu-migrator-' . sanitize_title($menu_object->name) . '.json';

            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo json_encode($export_data, JSON_PRETTY_PRINT);
            exit;
        }
    }

    // Logica di Importazione
    public function gab_handle_import() {
        if (isset($_POST['gab_submit_import']) && check_admin_referer('gab_import_action', 'gab_import_nonce')) {
            if (empty($_FILES['gab_import_file']['tmp_name'])) return;

            $data = json_decode(file_get_contents($_FILES['gab_import_file']['tmp_name']), true);
            if (!$data) return;

            // Nome richiesto: [Nome del menu] - Imported [ora]
            $new_menu_name = $data['name'] . ' - Imported ' . date('H:i');
            $menu_id = wp_create_nav_menu($new_menu_name);

            if (is_wp_error($menu_id)) return;

            $id_mapping = [];

            foreach ($data['items'] as $item) {
                $parent_id = ($item['parent'] != 0 && isset($id_mapping[$item['parent']])) ? $id_mapping[$item['parent']] : 0;
                
                // Fallback Link Personalizzato Relativo
                $menu_item_data = [
                    'menu-item-title'       => $item['title'],
                    'menu-item-url'         => (strpos($item['url'], '/') !== 0 && strpos($item['url'], '#') !== 0 && strpos($item['url'], 'http') === false) ? '/' . $item['url'] : $item['url'],
                    'menu-item-parent-id'   => $parent_id,
                    'menu-item-target'      => $item['target'],
                    'menu-item-classes'     => $item['classes'],
                    'menu-item-description' => $item['description'],
                    'menu-item-attr-title'  => $item['xfn'],
                    'menu-item-status'      => 'publish',
                    'menu-item-type'        => 'custom', 
                ];

                $slug = basename(untrailingslashit($item['url']));

                // Tentativo di ricollegamento a Post/Pagine o Taxonomies
                if ($item['type'] === 'post_type') {
                    $post = get_page_by_path($slug, OBJECT, $item['object']);
                    if ($post) {
                        $menu_item_data['menu-item-type'] = 'post_type';
                        $menu_item_data['menu-item-object'] = $item['object'];
                        $menu_item_data['menu-item-object-id'] = $post->ID;
                        unset($menu_item_data['menu-item-url']);
                    }
                } elseif ($item['type'] === 'taxonomy') {
                    $term = get_term_by('slug', $slug, $item['object']);
                    if ($term) {
                        $menu_item_data['menu-item-type'] = 'taxonomy';
                        $menu_item_data['menu-item-object'] = $item['object'];
                        $menu_item_data['menu-item-object-id'] = $term->term_id;
                        unset($menu_item_data['menu-item-url']);
                    }
                }

                $new_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_item_data);
                $id_mapping[$item['id']] = $new_item_id;
            }

            add_action('admin_notices', function() use ($new_menu_name) {
                echo '<div class="updated"><p>Success: the menu <strong>' . esc_html($new_menu_name) . '</strong> has been imported.</p></div>';
            });
        }
    }
}

// Inizializzazione del plugin
new Gab_Classic_Menu_Migrator();