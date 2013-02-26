<?PHP
/**
 * Document   : MongoDB Connection Class
 * Author     : josephtinsley
 * Description: My Goto MongoDB Connection Class I use to jump start my MongoDb projects.
 * http://twitter.com/josephtinsley 
*/

Class db {
 
   private $dbs, $docs;
   private $dbUser = "DBUserName";
   private $dbPass = "DBUserPassword";
   private $dbName = "DBName";
    
   CONST SALT      = 'supercalifragilisticexpialidocious';

   public function __construct(){
        try
        {
            $mongo = new Mongo("mongodb://$this->dbUser:$this->dbPass@localhost/$this->dbName");

            $this->dbs = $mongo->selectDB($this->dbName);
        }
        catch(MongoConnectionException $e)
        {                    
        die( 'Connection Failed' );
        }
    }
    
    /**
     * EXAMPLE QUERY<br>
     * SELECTS A OBJECT<br>
     * --------------------<br>
     * $collectionName = MyFriends";<br>
     * $criteria = array("first_name"=>"Dennis", "last_name"=>"Crowley");<br>
     * $sort = array("date"=>1);<br>
     * $limit = 25;<br>
     */
    public function selectDocument($collectionName, $criteria = array(), $sort = array(), $limit = 0){ 
        $cur = $this->dbs->$collectionName->find($criteria)->limit($limit);
        $cur->sort($sort);
        
        $this->docs = null;
        while( $docs = $cur->getNext()){
        $this->docs[] = $docs;  
        }
        return $this->docs;
    }

    /**
     * INSERT A OBJECT<br>
     * EXAMPLE INSERT QUERY<br>
     * --------------------<br>
     * $collectionName = MyFriends";<br>
     * $obj =  array("first_name"=>"Dennis", "last_name"=>"Crowley", "email" => "Denis.Crowley@gmail.com");<br> 
     */
    public function insertDocument($obj, $collectionName){
        $collection = $this->dbs->$collectionName;
        try{
            $collection->insert($obj, true);
            return  ( !empty($obj['_id']) )?1:0;
        } catch (MongoException $e) {
            return "Can't insert!\n";
        }      
    }    

    /**
     * UPDATES A OBJECT, CHECKS IF OBJECT WAS UPDATED, RETURNS #1 IF TRUE<br>
     * EXAMPLE UPDATE QUERY<br>
     * --------------------<br>
     * $collectionName = MyFriends";<br>
     * $criteria = array("first_name"=>"Dennis");<br>
     * $update   = array('$set' => array("last_name" =>"Sir. Crowley")  ); <br>
     * $confirm  = array("last_name" =>"Sir. Crowley");<br>
     */
    public function updateDocument($collectionName, $criteria, $update, $confirm){    
        if( empty($criteria)):
           return 0;
        endif;
        
        $collection = $this->dbs->$collectionName;
        try{
        $collection->update($criteria,$update, array("multiple" => true));
        
        $num_rows = $collection->find($confirm)->count();
        
        return ( !empty($num_rows) )?1:0;
        } catch (MongoException $e) {
            return "Can't update!\n";
        }         
    }    

    /**
     * DELETES A OBJECT, CHECKS IF OBJECT WAS DELETED, RETURNS #1 IF TRUE<br>
     * EXAMPLE DELETE QUERY<br>
     * --------------------<br>
     * $collectionName = 'MyFriends';<br>
     * $criteria = array("first_name"=>"Dennis");<br> 
     */
    public function removeDocument($collectionName, $criteria){    
        
        if( empty($criteria)):
           return 0;
        endif;
        
        $collection = $this->dbs->$collectionName;
        try{
        $collection->remove($criteria);
        
        $num_rows = $collection->find($criteria)->count();
        return ( empty($num_rows) )?1:0;
        } catch (MongoException $e) {
            return "Can't update!\n";
        } 
        
    }        
        
    /**
     * GENERATES A PIN CODE<br>
     */
    public function generatePinCode($collectionName, $field){

        $ranKeys = 'abcdefghijklmnopqrstuvwxyz123456789';
        do
        {
            $charLength = 20;
            for($x=0; $x < $charLength; $x++ ){
            $pos = mt_rand(0, 35);
            $key .= substr($ranKeys, $pos, 1);
            }
          
            $num_rows = $this->dbs->$collectionName->count( array($field => $key) );
            
            if( $num_rows >= 1 ):
                $keyStatus='GO'; // INUSE
            else:
                $keyStatus='STOP'; // NOTINUSE
            endif;

        }while ($keyStatus == "GO");

     return $key;
    }
    
} //END CLASS
?>
