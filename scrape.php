<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Scrape plugin
 *
 *
 * @author  James Doyle
 * @package Plugins
 */
class Plugin_Scrape extends Plugin {
  public $version = '1.0.0';

  public $name = array(
    'en'  => 'Scrape'
    );

  public $description = array(
    'en'  => 'A plugin to scrape a webpage for specific selectors.'
    );

  /**
   * Returns a PluginDoc array that PyroCMS uses
   * to build the reference in the admin panel
   *
   * All options are listed here but refer
   * to the Blog plugin for a larger example
   *
   * @return array
   */
  public function _self_doc()
  {
    $info = array(
      'find' => array(
        'description' => array(// a single sentence to explain the purpose of this method
          'en' => 'scrape a webpage for specific selectors.'
          ),
        'single' => true,// will it work as a single tag?
        'double' => true,// how about as a double tag?
        'variables' => '',// list all variables available inside the double tag. Separate them|like|this
        'attributes' => array(
          'url' => array(// this is the name="World" attribute
            'type' => 'text',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => '',// this attribute defaults to this if no value is given
            'required' => true,// is this attribute required?
            ),
          'cache_duration' => array(// this is the name="World" attribute
            'type' => 'int',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => '1440',// this attribute defaults to this if no value is given
            'required' => false,// is this attribute required?
            ),
          'selectors' => array(// this is the name="World" attribute
            'type' => 'text',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => '',// this attribute defaults to this if no value is given
            'required' => true,// is this attribute required?
            ),
          'attr' => array(// this is the name="World" attribute
            'type' => 'text',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => 'plaintext',// this attribute defaults to this if no value is given
            'required' => false,// is this attribute required?
            ),
          'cache' => array(// this is the name="World" attribute
            'type' => 'boolean',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => 'true',// this attribute defaults to this if no value is given
            'required' => false,// is this attribute required?
            ),
          'count' => array(// this is the name="World" attribute
            'type' => 'int',// Can be: slug, number, flag, text, array, any.
            'flags' => '',// flags are predefined values like asc|desc|random.
            'default' => 'counts parent results',// this attribute defaults to this if no value is given
            'required' => false,// is this attribute required?
            ),
          ),
        ),
      );

    return $info;
  }

  public function find()
  {
    // url to scrape
    $url = $this->attribute('url');
    // are we caching?
    $cache = $this->attribute('cache', true);
    // unique cache ID
    $id = 'scrape-'.md5($url);
    // default to 1 day
    $cache_duration = (int)$this->attribute('cache_duration', 1440);
    // load from cache if there is none, or cache is off
    if (!$data = $this->pyrocache->get($id) || !$cache) {
      // bust up the selectors
      $selectors = explode('|', $this->attribute('selectors'));
      // which attribute to get
      $attr = $this->attribute('attr', 'plaintext');
      require_once 'simple_html_dom.php';
      // Create DOM from URL
      $html = file_get_html($url);
      // Find all parent blocks
      $block = $html->find($selectors[0], 0);
      // remove the first selector because its just to get the parent
      array_shift($selectors);
      $count = $this->attribute('count', count($block->find($selectors[0])));
      $results = array();
      for ($i=0; $i < $count; $i++) {
        $item = array();
        foreach ($selectors as $selector) {
          $item[$selector] = $block->find($selector, $i)->{$attr};
        }
        $results[] = $item;
      }
      $this->pyrocache->write($results, $id, $cache_duration);
      // $this->pyrocache->delete($id);
      return $results;
    } else {
      return $this->pyrocache->get($id);
    }
  }
}