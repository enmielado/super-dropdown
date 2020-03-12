<?php
/**
 * Editables plugin for Craft CMS 3.x
 *
 * e
 *
 * @link      gs.com
 * @copyright Copyright (c) 2020 GS
 */

namespace veryfinework\superdropdown\variables;

use craft\elements\Category;
use craft\helpers\Json;
use craft\helpers\StringHelper;

/**
 * Editables Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.editables }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    GS
 * @package   Editables
 * @since     1.0.0
 */
class SuperdropdownVariable
{
    // Public Methods
    // =========================================================================

    /**
     * Whatever you want to output to a Twig template can go into a Variable method.
     * You can have as many variable functions as you want.  From any Twig template,
     * call it like this:
     *
     *     {{ craft.superdropdown.categoryCascade(catHandle, levels }}
     *
     * Or, if your variable requires parameters from Twig:
     *
     *     {{ craft.superdropdown.exampleVariable(twigValue) }}
     *
     * @param null $optional
     * @return string
     */
    public function test()
    {
        $result = "And away we go to the Twig template...";

        return $result;
    }

    public function categoryCascade($categoryGroupHandle, $maxLevels)
    {
        $categories = Category::find()
//            ->siteId($this->targetSiteId($element))
            ->group($categoryGroupHandle)
            ->level('<= '. $maxLevels)
            ->all();

        $dropdowns = [];

        $dropdowns['topLevel'] = [
            'name' => $categoryGroupHandle,
            'type' => 'primary',
            'options' => []
        ];

        foreach ($categories as $category) {

            $option = [
                'label' => $category->title,
                'value' => $category->id
            ];

            if ($category->hasDescendants && $category->level < $maxLevels) {

                $subselectName =  StringHelper::toKebabCase($category->title);

                $option['subselect'] = $subselectName;

                // create dropdown for subcategory with empty options
                if (!array_key_exists($category->title, $dropdowns)) {
                    $dropdowns[$subselectName] = [
                        'name' => $subselectName,
                        'type' => 'conditional',
                        'options' => []
                    ];
                }
            }

            $dropdownName = (bool)$category->parent
                ? StringHelper::toKebabCase($category->parent->title)
                : 'topLevel' ;

            $dropdowns[$dropdownName]['options'][] = $option;

        }

        return Json::encode($dropdowns);
    }




}
