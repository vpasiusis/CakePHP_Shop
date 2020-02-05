<?php
namespace App\Controller;
use Cake\Utility\Xml;
use SimpleXMLElement;
use App\Controller\AppController;
use Cake\Http\Client;
use Cake\I18n\Time;

/**
 * Products Controller
 *
 * @property \App\Model\Table\ProductsTable $Products
 *
 * @method \App\Model\Entity\Product[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProductsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index()
    {
        $products = $this->paginate($this->Products);

        $this->set(compact('products'));
    }

    /**
     * View method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => ['ProductRatings']
        ]);
        
        $allProducts =$this->Products ->find()->where(['id !=' => $id])->toList();
        $relatedProducts=$this->getRelatedProducts($allProducts,$product['name'],$product['description']);
        
        $this->set(compact('product','relatedProducts'));
    }
    /**
     * Method divides string into words and removes words which are shorter then 4 symbols 
     *
     * @param  String $string  Description or name string
     *
     * @return  stringsArray   divided words       
     */
    protected function divideString($string){
        $string=preg_replace("/[^A-Za-z0-9 ]/", '', $string);
        $array = explode(" ", $string);
        $arraydiff=array();
        foreach($array as $words){
            if(strlen($words)<4){
                array_push($arraydiff,$words);
            }
        }
        $array = array_diff($array,$arraydiff);
        return $array;
    }
    /**
     * Selects only related products to currently opened
     *
     * @param   Products  $productsList  Product list without opened one
     * @param   String  $name          Opened product name
     * @param   String  $description   Opened product description
     *
     * @return  Array                 returns array related to opened product, or empty array
     */
    protected function getRelatedProducts($productsList,$name,$description){
        $nameArray=$this->divideString($name);
        $descriptionArray=$this->divideString($description);
        $relatedProducts=array();
        $related=FALSE;
        foreach($productsList as $product){
            foreach($nameArray as $name){
                if(strpos($product['name'],$name)){
                    array_push($relatedProducts,$product);
                    $related=TRUE;
                break;
                }
            }
            if(!$related){
                foreach($descriptionArray as $descriptionWord ){
                    if(strpos($product['description'],$descriptionWord)){
                        array_push($relatedProducts,$product);
                        $related=TRUE;
                    break;
                    }
                }
            }
            $related=FALSE;
            
        }
        return $relatedProducts;
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $product = $this->Products->newEntity();
        if ($this->request->is('post')) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }
        $this->set(compact('product'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $product = $this->Products->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $product = $this->Products->patchEntity($product, $this->request->getData());
            if ($this->Products->save($product)) {
                $this->Flash->success(__('The product has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The product could not be saved. Please, try again.'));
        }
        $this->set(compact('product'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Product id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $product = $this->Products->get($id);
        if ($this->Products->delete($product)) {
            $this->Flash->success(__('The product has been deleted.'));
        } else {
            $this->Flash->error(__('The product could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    
    /**
     * Exports all products in XML
     *
     * @return  text/xml  opens new page with xml
     */
    public function exportxml()
    {
        $products = $this->paginate($this->Products);
        $productJson=json_encode($products, JSON_PRETTY_PRINT);
        $productArray = json_decode($productJson, true);
        $xml=$this->array2xml($productArray,false);
        return $this->response->withType('text/xml')->withStringBody($xml);
       
    }
    /**
     * Converts array to new XML
     *
     * @param  Products $array  products array
     * @param   $xml    new XML
     *
     * @return  reurns XML
     */
    function array2xml($array, $xml = false){

        if($xml === false){
            $xml = new SimpleXMLElement('<products/>');
        }
     
        foreach($array as $key => $value){
            if(is_array($value)){
                $this->array2xml($value, $xml->addChild(is_numeric((string) $key)?("n".$key):$key));
            } else {
                $xml->addChild(is_numeric((string) $key)?("n".$key):$key, $value);
            }
        }
     
        return $xml->asXML();
     }
    /**
     * Gets remote json data, and data to database
     *
     * @return  redirection to /index
     */
    public function importjson(){
        $http = new Client();

        $response = $http->get('https://raw.githubusercontent.com/wedeploy-examples/supermarket-web-example/master/products.json');
        $json = $response->json;

        foreach($json as $item) { 
            $this->Products->save($this->filterJson($item));
        }
        $this->Flash->success(__('Products has been imported.'));

        return $this->redirect(['action' => 'index']);

    }
    /**
     * Filters json object to Product entity
     *
     * @param Json  $json  data of object
     *
     * @return  returns new Product entity
     */
    protected function filterJson($json){
       
        $time = Time::now();
        $time=$time->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $product = $this->Products->newEntity();
        $product['name']=$json['title'];
        $product['price']=$json['price'];
        $product['description']=$json['description'];
        $product['photo']=$json['filename'];
        $product['created']=$time;
        $product['modified']=$time;
        return $product;

    }




    
}
