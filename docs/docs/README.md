# Super Dropdown plugin for Craft CMS 3.x
A custom field for the Craft CMS for building multiple and hierarchical dropdown fields from native elements or data.

![Screenshot](http://veryfine.work/assets/img/video1.gif)

## Requirements
This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation
You can install Super Dropdown via the Craft plugin store, or Composer.

### Craft Plugin Store
Navigate to the _Plugin Store_ section of your Craft Control Panel, search for `Super Dropdown`, and click the `Add To Cart`, `Install` , or `Try` button.

### Composer

1. From your project directory:

```sh
composer require veryfinework/super-dropdown
```
     
2. Either install the plugin from the Craft Control Panel under Settings > Plugins and click the `Install` button for Super Dropdown, or, finish the installation from the command line:
 
```sh
./craft install/plugin super-dropdown
```

## Overview
This plugin transforms structured data into series of linked dropdowns. The data source can be Categories, Entries, or any JSON data that is properly formatted. The JSON data may be static or dynamic. Dynamic data can be supplied by Twig templates using the complete Craft API. Static JSON can simply be pasted into the field definition, or provided by a Twig template.

### Things You Can Do With this Plugin
* Create a single dropdown from JSON data (static or dynamic)
* Create a set of adjacent dropdowns that are displayed and saved as a single field
* Create a set of drill-down style dropdowns based on hierarchical data (Categories, Entries, or custom)
* Create a sentence with a series fill-in-the-blank dropdowns all saved to a single field

### Advantages of a Super Dropdown Field
* Field options can be dynamic
* Simplify selecting Categories and Entries by replacing the the native modal selector with this field. (See 'Considerations' below.)
• Make field layouts in entry forms more compact by combining multiple fields into a single set of dropdowns.
• Skip the complications of coding linked dropdowns

## Using Super Dropdown

### Creating a Dropdown Field
1. After installing the plugin, create a new field in the Craft Control Panel Settings area and select `Super Dropdown` as the `Field Type`. 
2. Select a `Source`: Element, Template, or JSON. The `Source` selection will change the field's available options.
3. If you selected `Element` or `JSON’` then simply make selections from the options.
4. If you selected `Template` as the Source, then you need to add a template file to your site’s Template folder that outputs JSON data. It is recommenced to store your Super Dropdown field templates in their own folder. If you create a folder in your `Templates` folder named `_fieldData` and create a file within that folder named `myDropdown.twig` then the value of the `Template` field would be `_fieldData/myDropdown`. See the information below for providing properly formatted data from a Twig template.

### Data Sources
As long as the JSON is formatted using the correct structure and keys, any data source may be used. You can provide data in 3 ways:

* Select a Category Group or Entry Section that will render a drill-down style list of dropdowns.
* Provide a path to a Twig template where you can access the complete Craft API to generate JSON for the dropdown options
* Paste static JSON directly into the field settings

### Other Options
* `Layout` - Set series of dropdowns side-by-side or vertically stacked.
* `Include Blank Option` - dropdowns generated from Craft elements include an option to add a blank option for subcategories.
* `Label Length` - Truncate long Category or Entry titles to make them practical as dropdown field options.
* `Maximum Nesting Level` -  Limit the nesting level of Categories and Entries.

### Considerations
Please note that this plugin is not a drop-in replacement for the native Entries and Categories fields. If you change one of those fields to this field type, the data will not be carried over. Technically, this is not what Craft refers to as a 'relational' field, which means that it does not create a database relation between elements.

## Accessing Field Values in Templates
Field are returned as either a hash, or in the case of Elements (Categories & Entries) they may optionally be returned as an instance of an Element. 

### Return Value as a Hash
Hashes use the `name` of the dropdown sets as keys. In any series of dropdowns, one with no active selection will not be included in the hash.

To view all values in a  hash, drop this code into a template:
    
```twig
<ul>
{% for key, value in entry.theSuperDropdownField %}
    <li>{{ key }} : {{ value }}</li>
{% endfor %}
</ul>
```

To render all the values of a hash from a dropdown field:
    
```twig
{{ entry.dropdownField | join(' —> ') }}
```
    
To retrieve a a single value from the hash use this format:

```twig
{{ entry.theSuperDropdownField.arrayKey }}
```

In the case of the Category dropdowns, the keys are kebab case strings of the parent category title, except for the top-level category which will use the category group handle as its key.

To get the value of the final array item, sometimes useful if you built a category or entry dropdown and only want the final category/entry use this:

```twig
{{ entry.theSuperDropdownField | last }}
```

By default,  Category dropdowns return a string with the id and title joined by a colon:

    1690:The Category Title
    
To et a Category Element from a dropdown field:

```twig
{% set catId = entry.dropdownField | last | split(’:’) | first %}

{% category = craft->app->categories->getById(catId) &}
```

### Return Value as an Element
For fields with a `Source` of ‘Element,’ there is an option to return the value as an instance of the element. In that case, you would, for example, output a category title like so:

```twig
{{ entry.dropdownCatgeoryField.title }}
```

## Data Sources

### JSON Data Formatting Rules
* All dropdown data should be provided in flat series. The data should not be nested. Nesting is managed by associated keys.
* You must use the keys as shown in the example below: `name`, `type`, `options`, `label`, `value`, `default`, `subselect`, `label`, `beforeText`, `afterText`, and `optgroup`.
* Top-level dropdowns (ones not leafs of other dropdowns) should have an attribute of `type` set to `primary`.
* The leaf of an `option` is indicated by adding an attribute of `subselect` set to the `name` of its associated dropdown (which will appear lower in the JSON data).
* Leafs set their `name` to the appropriate key as described above.
* There is no technical limit to the number of levels. Any option can have a `subselect` attribute.
* Options can share leafs by setting their `subselect` attribute to the `name` of the same leaf.
* Default values may be set on an `option` by adding an attribute of `default` set to `true`.

### Example Data
See the `/resources/templates` folder in this plugin’s source for example code snippets that you can copy into your templates. There are ready-to-go templates for:

* A semester and year picker for next 5 years, dynamically generated
* A dessert selector that demonstrates proper format for static JSON
* A single category pulled from the Craft Category service. (You can accomplish this more easily by using field options, but this template may be a model for building more complex options.)
* A series of cascading dropdowns drawn from Categories. (You can accomplish this more easily by using field options, but this template may be a model for building more complex options.)
* A series of cascading dropdowns drawn from Entries. (You can accomplish this more easily by using field options, but this template may be a model for building more complex options.)

### JSON Data Examples

For more examples, see the `resources\templates` folder of this plugin’s source. 

A basic example of static dropdown data that will produce one dropdown and one leaf.

```json
[
  {
    "name": "dessert",
    "type": "primary",
    "options": [
      {
        "label": "Pie",
        "value": "pie",
        "subselect": "pie-type"
      },
      {
        "label": "Cake",
        "value": "cake",
      },
      {
        "label": "Large",
        "value": "large"
      }
    ]
  },
  {
    "name": "pie-type",
    "options": [
    {
      "label": "Blueberry",
      "value": "blueberry"
    },
    {
      "label": "Apple",
      "value": "apple"
    }
  ]
  }
]
```

A template that renders dynamic JSON data for a semester picker. Notice the ‘type’ of ‘primary’ for both dropdowns. Put this in a Twig template to test it.

```twig
{%- spaceless -%}

  {% set startYear = "now"|date("Y") %}
  {% set endYear = "now"|date("Y") + 5 %}
  {% set years = [] %}

  {% for year in startYear..endYear %}

    {% set yearOption = [{
      "label": year,
      "value": year
    }] %}

    {% set years = years|merge( yearOption ) %}

  {% endfor %}

  {% set dropdowns = [
    {
      "name": "semester",
      "type": "primary",
      "options": [
      {
        "label": "Fall",
        "value": "fall"
      },
      {
        "label": "Spring",
        "value": "spring"
      }
    ]
    },
    {
      "name": "year",
      "type": "primary",
      "options": years
    }
  ] %}

  {{ dropdowns | json_encode() | raw }}

{%- endspaceless -%}
```

## Roadmap
* Support for more Craft Elements
* More fine-grained options
* Listening to your requests

Brought to you by [Very Fine](http://veryfine.work)

