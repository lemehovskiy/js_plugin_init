<?php

$shortopts = "";

$longopts = array(
    "init:",
    "install::",
    "destroy::"
);

$options = getopt($shortopts, $longopts);

$config_json = file_get_contents("js_plugin_init_config.json");

$config = json_decode($config_json, true);


//define("THEME_DIRECTORY", 'wp-content/themes/' . $config['project_name'] . '-theme');
//define("PROJECT_NAME_UNDERSCORE",  str_replace('-', '_', $config['project_name']));


if (isset($options['init'])) {
    $config['project_name'] = $options['init'];

    create_project_folder($config);

} else if ($config['project_name'] != null) {
    if (isset($options['install'])) {



        create_main_js_file($config);

        create_gulp_file($config);

        create_package_json($config);

        create_readme_file($config);

//        create_gitignore($config);

//        git_init($config);


        if (isset($options['destroy'])) {
            remove_init_files();
        }
    } else if (isset($options['destroy'])) {
        remove_init_files();
    } else {

    }
}

else {

}


function create_readme_file($config){
    $search_fields = array(
        '{PROJECT_NAME}'
    );

    $replace_with = array(
        $config['project_name']
    );


    create_file_by_sample(array(
        'sample_file' => "js_plugin_init_src/core/README.md",
        'create_file' => 'README.md',
        'search_field' => $search_fields,
        'replace_field' => $replace_with
    ));

}


function create_package_json($config){

    $package_json = file_get_contents("js_plugin_init_src/core/package.json");

    $package_config = json_decode($package_json, true);

    $package_config['name'] = $config['project_name'];
    $package_config['description'] = $config['project_description'];
    $package_config['main'] = 'build/'. $config['project_name']. '.es6';
    $package_config['keywords'] = $config['project_keywords'];
    $package_config['repository']['url'] = 'https://github.com/lemehovskiy/'. $config['project_name'];

    //create config file
    $fp = fopen('package.json', 'w');
    fwrite($fp, json_encode($package_config, JSON_PRETTY_PRINT));
    fclose($fp);

}


function create_main_js_file($config){
    $search_fields = array(
        '{CLASS_NAME}',
        '{JQUERY_FUNCTION_NAME}',
        '{FUNCTION_NAME_UNDERSCORE}',
    );

    $replace_with = array(
        $config['project_class'],
        $config['jquery_function_name'],
        $config['function_name_underscore']
    );


    create_file_by_sample(array(
        'sample_file' => "js_plugin_init_src/core/plugin.es6",
        'create_file' => 'src/' . $config['project_name'] . '.es6',
        'search_field' => $search_fields,
        'replace_field' => $replace_with
    ));

}

function create_gulp_file($config){
    $search_fields = array(
        '{PROJECT_NAME}'
    );

    $replace_with = array(
        $config['project_name']
    );


    create_file_by_sample(array(
        'sample_file' => "js_plugin_init_src/core/gulpfile.js",
        'create_file' => 'gulpfile.js',
        'search_field' => $search_fields,
        'replace_field' => $replace_with
    ));

}

function create_file_by_sample($settings){

    $dirname = dirname($settings['create_file']);

    if (!is_dir($dirname))
    {
        mkdir($dirname, 0755, true);
    }

    $sample_file = file_get_contents($settings['sample_file']);

    $sample_file_replaced_fields = str_replace($settings['search_field'], $settings['replace_field'], $sample_file);

    $create_file = fopen($settings['create_file'], 'w');
    fwrite($create_file, $sample_file_replaced_fields);

}


function create_folder($path){
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}


function git_init(){
    system('git init');
    system('git add .');
    system('git commit -m "init"');
}


function create_gitignore($config)
{

    $gitignore_string = "";

    $rules_counter = 0;

    foreach ($config['gitignore'] as $rule) {

        if ($rules_counter++ == 0) {
            $gitignore_string .= $rule;
        } else {
            $gitignore_string .= "\n" . $rule;
        }

    }

    //create config file
    $fp = fopen(".gitignore", 'w');
    fwrite($fp, $gitignore_string);
    fclose($fp);


}


function remove_starter_theme_files($config)
{

    $full_paths = array();

    foreach ($config['remove_starter_theme_files'] as $file) {
        $full_paths[] = THEME_DIRECTORY . '/' . $file;
    }

    remove_files($full_paths);
}


function include_taxonomies_to_core($config)
{

    $taxonomy_path_string = "// TAXONOMIES";

    foreach ($config['taxonomies'] as $taxonomy) {
        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['slug']);

        $taxonomy_path_string .= "\n" . 'include("taxonomies/register_taxonomy_' . $taxonomy_slug_underscore . '.php");';

    }

    $file = file_get_contents(THEME_DIRECTORY . "/core/core.php");
    $file = str_replace('// TAXONOMIES', $taxonomy_path_string, $file);
    file_put_contents(THEME_DIRECTORY . "/core/core.php", $file);

}

function include_post_types_to_core($config)
{

    $post_type_path_string = "// POST TYPES";

    foreach ($config['post_types'] as $post_type) {
        $taxonomy_slug_underscore = str_replace('-', '_', $post_type['slug']);

        $post_type_path_string .= "\n" . 'include("post_types/register_post_type_' . $taxonomy_slug_underscore . '.php");';

    }

    $file = file_get_contents(THEME_DIRECTORY . "/core/core.php");
    $file = str_replace('// POST TYPES', $post_type_path_string, $file);
    file_put_contents(THEME_DIRECTORY . "/core/core.php", $file);

}

function create_taxonomies($config)
{

    if (!isset($config['taxonomies'])) {
        return;
    }

    foreach ($config['taxonomies'] as $taxonomy) {

        $taxonomy_slug_underscore = str_replace('-', '_', $taxonomy['slug']);

        $searchF = array(
            '{TAXONOMY_SLUG}',
            '{TAXONOMY_NAME}',
            '{TAXONOMY_SINGULAR_NAME}',
            '{ASSIGN_TO_POST_TYPE}',
            '{TAXONOMY_SLUG_UNDERSCORE}'
        );

        $replaceW = array(
            $taxonomy['slug'],
            $taxonomy['name'],
            $taxonomy['singular_name'],
            $taxonomy['assign_to_post_type'],
            $taxonomy_slug_underscore
        );


        create_file_by_sample(array(
            'sample_file' => "wp-init-src/core/register_taxonomy.php",
            'create_file' => THEME_DIRECTORY . '/core/taxonomies/register_taxonomy_' . $taxonomy_slug_underscore . '.php',
            'search_field' => $searchF,
            'replace_field' => $replaceW
        ));

    }

    include_taxonomies_to_core($config);

}

function create_post_types($config)
{

    if (!isset($config['post_types'])) {
        return;
    }

    foreach ($config['post_types'] as $post_type) {

        $post_type_slug_underscore = str_replace('-', '_', $post_type['slug']);

        $searchF = array(
            '{POST_TYPE_SLUG}',
            '{POST_TYPE_NAME}',
            '{POST_TYPE_SINGULAR_NAME}',
            '{POST_TYPE_SLUG_UNDERSCORE}'
        );

        $replaceW = array(
            $post_type['slug'],
            $post_type['name'],
            $post_type['singular_name'],
            $post_type_slug_underscore
        );

        create_file_by_sample(array(
            'sample_file' => "wp-init-src/core/register_post_type.php",
            'create_file' => THEME_DIRECTORY . '/core/post_types/register_post_type_' . $post_type_slug_underscore . '.php',
            'search_field' => $searchF,
            'replace_field' => $replaceW
        ));

    }

    include_post_types_to_core($config);

}

function install_plugins($config)
{
    //copy local plugins
    foreach ($config['local-plugins'] as $plugin) {
        if ($plugin['install']) {
            system('cp -r ' . $plugin['path'] . ' ' . 'wp-content/plugins');
        }
    }

    //download remote plugins
    foreach ($config['remote-plugins'] as $plugin) {
        if ($plugin['install']) {

            //download
            system('curl -L -o remote-plugin.zip ' . $plugin['url']);

            //extract and remove
            system('tar -xvf remote-plugin.zip --directory wp-content/plugins && rm remote-plugin.zip');

        }
    }
}



function remove_files($files)
{
    foreach ($files as $file) {
        system('rm -rf ' . $file);
    }
}



function create_project_folder($config)
{

    $path = '../' . $config['project_name'];

    if (is_dir($path)) {
        throw new \RuntimeException(sprintf('Unable to create the %s directory', $path));
    } else {

        //create project folder
        system('mkdir -p ' . $path);

        //create config file
        $fp = fopen($path . '/js_plugin_init_config.json', 'w');
        fwrite($fp, json_encode($config, JSON_PRETTY_PRINT));
        fclose($fp);

        //copy src files
        system('cp -r js_plugin_init_src ' . $path);

        //copy init file
        system('cp -r js_plugin_init.php ' . $path);


        echo 'Successfully init' ."\r\n";
    }

}

function remove_init_files()
{
    remove_files(array(
        'js_plugin_init.php',
        'js_plugin_init_src',
        'js_plugin_init_config.json'
    ));
}