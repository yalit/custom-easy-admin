# Target update

The list below is the elements I want to make work in the admin using only EasyAdmin and integration with Symfony.

Please feel free to propose new actions/functionalities to be added to the implementation.

I'll try to define specific branches with each time the updates made for a specific modification and link to the branch name here... The main branch will contain all the modifications.

## CRUD level
### 1. Security
- restrict access at entity level ==> done in branch : 02.Post_Workflow_Listing
- restrict access at Action level ==> done in branch : 01.only_admin_can_update_users 

### 2. Fields
- custom Field within listing/index ==> done in branch : 02.Post_Workflow_Listing
- custom Field within Form (new/update) ==> done in branch : 03.custom_collection_field_form
- unmapped field on specific entity ==> done in branch : 02.Post_Workflow_Listing

### 3. Listing
#### a. Data table
- show only a part of the data (filtering)
  - based on security ==> done in branch : 02.Post_Workflow_Listing
  - based on queryBuilder
- specific sort of data tables

#### b. Filters
- define custom Filters

#### b. Actions
- define custom entity action ==> done in branch : 02.Post_Workflow_Listing
  - including export of data into csv/excel using filters
- define custom bulk action

### 4. Forms
- custom form template/design
- multiple page form (using js)
- custom redirect post form submission

## Dashboard level
- create a custom dashboard template
- create a specific action page 

### 5. Assets
- specific assets (js/css) at all levels
  - addition of assets for specific CRUD ==> done in branch : 02.Post_Workflow_Listing


I'll try to document it also as a blog posts here : [Yalit's Blog][1]
[1]: https://yalit.be/blog