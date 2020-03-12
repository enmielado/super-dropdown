# Super dropdown plugin for Craft CMS 3.x

Adds a field type to generate dropdowns in cascades and series.

![Screenshot](resources/img/super-dropdown-icon6.png)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require veryfinework/super-dropdown

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Super dropdown.

## Super dropdown Overview

Put any structured data into series and cascading dropdown selects.

This plugins adds a fieldtype with which you can create a single field that combines a number of dropdowns. You can create a drilldown style list of options. You can create multiple dropdowns that combine into a single value (e.g., a semester picker that has a 'semester' dropdown and a 'year' dropdown.) You can also use it to generate a single dropdown populated with any JSON data (static or dynamic).

### What You Can Do With this Plugin
- Create a single dropdown form JSON data (static or dynamic)
- Create adjacent dropdowns that are displayed and saved as a single field
- Create a set of drill-down style dropdowns based on hierarchical data

### The Advantages of a Super dropdown
- Dynamic data
- Simplified selections for users
- Make more compact field layouts in entry forms by combining multiple fields into one set of dropdowns.

## Using Super dropdown
You can provide data in 3 ways:
- Paste static JSON pasted into the field settings
- Provide a path to a template where you can access of all of Craft to generate the JSON for the dropdown options
- Select a Category Group that will render a drill-down style list of dropdowns.

As long as the JSON is formatted using the correct structure and keys, any data source may be used.

## Other Notes
- Category dropdowns include an option to add a blank option for subcategories.
- Truncate long category or entry titles to make them practical for dropdowns.
- The category and entry section options are conveniences, but using the Craft api in twig means you can populate dropdowns with any structured data.

### Examples
See the /resources/templates folder in this plugin's source for example code snippets that you can copy into your templates. There are ready-to-go templates for:
- A Dessert selector that demonstrates proper format for the JSON
- A semester and year picker for next 5 years, dynamically generated
- A template that demonstrates how to combine series and cascading dropdowns

### JSON Formatting Rules
- All dropdowns data should be provided in a flat series. The data is not nested. Nesting is handled by associated keys.
- You must use the keys as shown below: 'name', 'type', 'options', 'label', 'value', 'default', 'subselect'.
- Top-level dropdowns (ones not leafs of other dropdowns) should have an attribute of 'type' set to 'primary'.
- The leaf of an option is indicated by adding an attribute of 'subselect' set to the 'name' of its associated dropdown (which appears lower in the JSON).
- Leafs set their 'name' to the appropriate key as described above.
- There is no technical limit to the number of levels. Any option can have a 'subselect' attribute.
- Options can share leafs by setting their 'subselect' attribute to the 'name' of the same leaf.
- Default values may be set on an option by adding an attribute of 'default' set to true.


A basic example of static dropdown data that will produce one dropdown and one leaf.

    [
      {
        "name": "dessert",
        "type": "primary",
        "options": [
          {
            "label": "Pie",
            "value": "pie"
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

A template that renders dynamic JSON data for a semester picker. Notice the 'type' of 'primary' for both dropdowns.

    {# Side-by-side semester and year dropdowns for the next 5 years #}
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



## Accessing Values in Templates

Field is returned as an associative array using the 'name' of the dropdowns as keys. The array will only contain values for dropdowns that are visible when the element is saved.

In the case of the Category Group dropdowns, keys are kebab case strings of the category title, except for the top-level category which will use the category group handle as its key.

    {{ entry.theDropdownField.arrayKey }}

Take a look at the array:
    
    <ul>
    {% for key, value in entry.theDropdownField %}
        <li>{{ key }} : {{ value }}</li>
    {% endfor %}
    </ul>
    
Get the last value, sometimes useful if you built a category dropdown and just want the final category:

    {{ entry.semester | last }}

Category dropdowns return a string with the id and title joined by a colon:

    1690:The Category Title
    
Get a category from a dropdown field

    {% set catId = entry.dropdownField | last | split(’:’) | first %}
    
    {% cat = craft->app->categories->getById(catId) &}
    
    {{ entry.dropdownField | join(‘ —> ') }}

## Super dropdown Roadmap

- More options!
- Fulfilling your requests

Brought to you by [veryfinework](https://github.com/veryfinework)
