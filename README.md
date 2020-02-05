# Product Shop(Task 2)

Web App made with CakePHP. Consists of products listing, products ratings, ability to export products in XML, ability to import from [remote json](https://raw.githubusercontent.com/wedeploy-examples/supermarket-web-example/master/products.json) , and method which gets related products.

## Installation

App works good on XAMPP, you need to create database in localhost(name 'shop'). Recommended to install CakePHP for database seeding. [CakePHP](https://book.cakephp.org/3.next/en/installation.html). If CakePHP is installed: use these commands to seed database.
```unix
"vendor/bin/phinx.bat" seedrun -e development -s ProductSeed
"vendor/bin/phinx.bat" seedrun -e development -s ProductRatingsSeed
```


## Usage

```php
/products
returns list of Products

/products/exportxml
returns text/xml of Products list

Method "importjson()"  in ProductControler
imports data from remote json, returns index view(/products)

Method "getRelatedProducts()"  in ProductControler 
Selects products which are related to opened product in view
```


### Tools
Visual Studio Code, Git, CakePHP, Phinx, XAMPP
