<?php
/**
 * Super dropdown plugin for Craft CMS 3.x
 *
 * Adds a field type that generates side-by-side and cascading dropdowns from data.
 *
 * @link      https://github.com/veryfinework
 * @copyright Copyright (c) 2020 veryfinework
 */

namespace veryfinework\superdropdown\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Json;
use craft\elements\Category;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use craft\helpers\Html;
use craft\helpers\StringHelper;
use craft\models\Section;
use craft\web\View;

use veryfinework\superdropdown\assetbundles\superdropdownfield\SuperdropdownFieldAsset;


/**
 * Superdropdown Field
 *
 *
 * @author    veryfinework
 * @package   Superdropdown
 * @since     1.0.0
 *
 * @property array $entries
 * @property mixed $settingsHtml
 * @property array $sourceOptions
 * @property array $categories
 */
class Superdropdown extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * category group id string
     *
     * @var string
     */
    public $categoryGroup;

    /**
     * entry section id string
     *
     * @var string
     */
    public $entrySection;

    /**
     * Source: 'jsonData' or 'template' or 'element'
     *
     * @var string
     */
    public $sourceType = '';

    /**
     * How the fields are arranged
     *
     * @var string
     */
    public $layout = 'inline';

    /**
     * @var null
     */
    public $elementType = 'categories';

    /**
     * Character limit on labels for elements
     *
     * @var string
     */
    public $labelLength = 30;

    /**
     * Level limit on structures for Elements
     *
     * @var string
     */
    public $maxNestingLevel = 3;

    /**
     *
     *
     * @var string
     */
    public $queryParams = '';

    /**
     * Include a blank option in category dropdowns
     *
     * @var string
     */
    public $blankOption = false;

    /**
     * JSON data
     *
     * @var string
     */
    public $jsonData = '';

    /**
     * Path to frontend template that returns JSON
     *
     * @var string
     */
    public $template = '';

    // Static Methods
    // =========================================================================

    /**
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('super-dropdown', 'Super Dropdown');
    }

    /**
     * @inheritdoc
     */
    public static function valueType(): string
    {
        return 'array';
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * More info: http://www.yiiframework.com/doc-2.0/guide-input-validation.html
     *
     * @return array
     */

    // rules for selections made by an entry editor
//    public function rules()
//    {
//        $rules = parent::rules();
//        $rules = array_merge($rules, [
//            ['jsonData', 'string']
//        ]);
//        return $rules;
//    }

    /**
     * @inheritDoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        if (is_string($value) && !empty($value)) {
            $value = Json::decodeIfJson($value);
        } else if ($value === null && $this->isFresh($element)) {
            $value = [];
        }

        if (!is_array($value)) {
            return null;
        }

        // remove any empty values caused by blank options
        foreach ($value as $key => $val) {
            if ($val === '') {
                unset($value[$key]);
            }
        }

        return $value;
    }

    /**
     * Normalizes the available sources into select input options.
     *
     * @return array
     */
    public function getSourceOptions(): array
    {
        $availableCategoryGroups = Craft::$app->getElementIndexes()->getSources(Category::class, 'modal');
        $availableSections = Craft::$app->getElementIndexes()->getSources(Entry::class, 'modal');

        return [
            'categories' => $this->makeOptionsFromSources($availableCategoryGroups),
            'sections' => $this->makeOptionsFromSources($availableSections)
        ];
    }

    public function makeOptionsFromSources($sources): array
    {

        $options = [];
        $optionNames = [];

        foreach ($sources as $source) {
            // skip headings
            if (!isset($source['heading'])) {
                $options[] = [
                    'label' => Html::encode($source['label']),
                    'value' => $source['key']
                ];
                $optionNames[] = $source['label'];
            }
        }

        // Sort alphabetically
        array_multisort($optionNames, SORT_NATURAL | SORT_FLAG_CASE, $options);

        return $options;

    }

    /**
     * @inheritDoc
     */
    public function getSettingsHtml()
    {
        // Render the settings template
        return Craft::$app->getView()->renderTemplate(
            'super-dropdown/_components/fields/Superdropdown_settings',
            [
                'field' => $this,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        $view = Craft::$app->getView();

        // Register our asset bundle
        $view->registerAssetBundle(SuperdropdownFieldAsset::class);

        // Get our id and namespace
        $id = $view->formatInputId($this->handle);
        $namespacedId =$view->namespaceInputId($id);

        $fieldSettings = $this->getSettings();

        $sourceType = $fieldSettings['sourceType'];

        switch ($sourceType) {
            case 'element':
                $elementsAndName = ($this->elementType === 'entries') ? $this->getEntries() : $this->getCategories();
                $dropdownsArray = $this->convertElementsToDropdownsArray($elementsAndName['elements'], $elementsAndName['topLevelName']);
                break;

            case 'template':
                $oldMode = $view->getTemplateMode();
                $view->setTemplateMode(View::TEMPLATE_MODE_SITE);

                $dropdownsArray = Json::decode( $view->renderTemplate(
                    $fieldSettings['template']
                ));

                $view->setTemplateMode($oldMode);
                break;

            case 'jsonData':
            default:
                $dropdownsArray = Json::decode($fieldSettings['jsonData']);

        }

        // prep array for creating select inputs
        $allDropdowns = $this->prepDropdownsForInputHtmlTemplate($dropdownsArray, $value);

        // Variables to pass to JavaScript
        $jsonVars = [
            'id' => "{$namespacedId}-field",
            'editable' => []
            ];
        $jsonVars = Json::encode($jsonVars);
        $view->registerJs('window.CE_Superdropdown(' . $jsonVars . ');');

        // Render the input template
        return $view->renderTemplate(
            'super-dropdown/_components/fields/Superdropdown_input',
            [
                'name' => $this->handle,
                'id' => $id,
                'class' => 'layout-'.$this->layout,
                'value' => $value,
                'field' => $this,
                'namespacedId' => $namespacedId,
                'dropdowns' => $allDropdowns,
            ]
        );
    }


    /**
     *
     * Get categories based on section
     *
     * @return array
     */
    public function getCategories(): array
    {

        $groupUId = StringHelper::afterLast($this->categoryGroup, ':');
        $group = Craft::$app->categories->getGroupByUid($groupUId);
        $maxLevels = $this->maxNestingLevel ? '<= '. $this->maxNestingLevel : null;

        $categories = Category::find()
            ->group($group)
            ->level($maxLevels)
            ->all();

        // Fill in any gaps
        $categoriesService = Craft::$app->getCategories();
        $categoriesService->fillGapsInCategories($categories);

        return [
            'topLevelName' => $group->handle,
            'elements' => $categories
        ];

    }

    /**
     *
     * Get entries based on section
     *
     * @return array
     */
    public function getEntries(): array
    {
        $entryQuery = Entry::find();

        $maxLevels = $this->maxNestingLevel ? '<= ' . $this->maxNestingLevel : null;

        switch ($this->entrySection) {
            case '*':
                $entryQuery->withStructure()->level($maxLevels);
                $topLevelName = 'entries';
                break;

            case 'singles':
                $sectionsArray = Craft::$app->sections->getSectionsByType(Section::TYPE_SINGLE);
                $sectionIds = ArrayHelper::getColumn($sectionsArray, 'id');
                $entryQuery->sectionId($sectionIds);
                $topLevelName = 'singles';
                break;

            default:
                $sectionUId = StringHelper::afterLast($this->entrySection, ':');
                $section = Craft::$app->sections->getSectionByUid($sectionUId);
                $entryQuery->sectionId($section->id);
                if ($section->type === Section::TYPE_STRUCTURE) {
                    $entryQuery->withStructure()->level($maxLevels);
                }
                $topLevelName = $section->handle;
        }

        return [
            'topLevelName' => $topLevelName,
            'elements' => $entryQuery->all()
        ];
    }

    /**
     *
     * Prepare entry data for use by the template
     *
     * @return array
     */
    public function convertElementsToDropdownsArray($elements, $topLevelName): array
    {

        $dropdowns = [];

        $dropdowns[$topLevelName] = [
            'name' => $topLevelName,
            'type' => 'primary',
            'options' => []
        ];

        foreach ($elements as $element) {

            $label = $this->labelLength ? StringHelper::truncate($element->title, $this->labelLength) : $element->title;

            $option = [
                'label' => $label,
                'value' => $element->id . ':' . $element->title
            ];

            // set select array key for for categories/entries
            if ($this->elementType === 'categories') {
                $selectName = (bool)$element->parent ? StringHelper::toKebabCase($element->parent->title) : $topLevelName;
                $subselectName =  StringHelper::toKebabCase($element->title);
            } else {
                $selectName = (bool)$element->parent ? $element->parent->id : $topLevelName;
                $subselectName = $element->id;
            }

            // create subselect array
            if ($element->hasDescendants && $element->level < $this->maxNestingLevel) {

                $option['subselect'] = $subselectName;

                // create dropdown for subcategory with empty options
                if (!array_key_exists($element->title, $dropdowns)) {
                    $dropdowns[$subselectName] = [
                        'name' => $subselectName,
                        'type' => 'conditional',
                        'options' => []
                    ];
                }
            }

            $dropdowns[$selectName]['options'][] = $option;

        }

        return $this->addBlankOptions($dropdowns);
    }

    public function addBlankOptions(&$dropdowns) {

        // add blank options, skip the first
        if ($this->blankOption) {
            $first = true;
            foreach ($dropdowns as &$dropdown) {
                if ($first) {
                    $first = false;
                    continue;
                }
                array_unshift($dropdown['options'], [
                    'label' => '--- select ---',
                    'value' => ''
                ]);
            }
        }

        return $dropdowns;
    }

    /**
     *
     * Prepare JSON data for use by the template
     *
     * @param $dropdowns
     * @param $value
     * @return array
     */
    public function prepDropdownsForInputHtmlTemplate($dropdowns, $value): array
    {

        $allDropdowns = [];
        $conditionalSubselectKeys = [];

        foreach ($dropdowns as &$dropdown) {

            $key = $dropdown['name'];
            $savedValue =  (!empty($value) && array_key_exists($key, $value)) ? $value[$key] : null;

            if ($savedValue === null) {
                if (array_key_exists('type', $dropdown) && $dropdown['type'] === 'primary') {
                    $dropdown['initialvalue'] = '0';
                } else {
                    $dropdown['initialvalue'] = '-1';
                }
            }

            foreach ($dropdown['options'] as $index => &$option) {

                if(array_key_exists('subselect', $option)) {
                    $conditionalSubselectKeys[] = $option['subselect'];
                }

                // set selected
                if (($option['value'] !== null && $option['value'] === $savedValue)
                    || ($savedValue === null && isset($option['default']) )
                ) {
                    $option['selected'] = true;
                    $dropdown['initialvalue'] = $index;
                }

            }

            $allDropdowns[$dropdown['name']] = $dropdown;

        }

        foreach ($conditionalSubselectKeys as $conditionalSubselectKey) {
            $allDropdowns[$conditionalSubselectKey]['isConditional'] = true;
        }

        return $allDropdowns;

    }

    /**
     * Transforms a nested array of dropdowns into a flattened array
     *
     *
     * @param $dataArray
     * @param $value
     * @return array
     */
    public function cascadingDropdowns($dataArray, $value): array
    {

        $allDropdowns = [];

        $makeDropdown = static function( $dropdown, $level ) use ( &$makeDropdown, &$allDropdowns ) {

            $dropdown['level'] = $level; // unused
            $allDropdowns[$dropdown['name']] = $dropdown;

            foreach ($dropdown['options'] as &$option) {
                if(array_key_exists('subselect', $option)) {

                    $subDropdown = $option['subselect'];
                    $subDropdown['isConditional'] = true;

                    $makeDropdown($subDropdown, $level+1);
                }
            }
        };

        foreach ($dataArray as $topLevelDropdown) {
            $makeDropdown($topLevelDropdown, 0);
        }

        // use $value to set selected options
        foreach ($allDropdowns as &$dropdown) {
            $key = $dropdown['name'];

//        Craft::info($allDropdowns, 'multi');

            $savedValue =  (!empty($value) && array_key_exists($key, $value)) ? $value[$key] : null;

            foreach ($dropdown['options'] as &$option) {

                // set selected
                if ($option['value'] === $savedValue
                    || ($savedValue === null && isset($option['default']) )
                ) {
                    $option['selected'] = true;
                }

                // make relevant children active
                if(array_key_exists('subselect', $option)) {

                    // show sub-dropdown if parent is selected
                    if (array_key_exists('selected', $option)) {
                        $allDropdowns[$option['subselect']['name']]['active'] = true;
                    }

//                    $option['hasChild'] = $option['subselect']['name'];
                    $option['subselect'] = $option['subselect']['name'];
//                    unset($option['child']);
                }


            }
        }

        return $allDropdowns;
    }
}
