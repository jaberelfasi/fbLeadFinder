<?php



class Dao{
    private $servername;
    private $username;
    private $password;
    private $dbname;
    private $connection;
    
    //construct
    public function __construct($host, $user, $pass, $db) {
        $this->servername=$host;
        $this->username=$user;
        $this->password=$pass;
        $this->dbname=$db;
    }
    
    public function addFacebookPages($search){
        $this->openConnection();
        $max= sizeof($search);
        $sumRecords=0;
        $errorCount=0;
        foreach ($search as $key) {
            $name=$key['name'];
            $id=$key['id'];
            $sql = "INSERT INTO page_names (name, page_id) VALUES ('".addslashes($name)."','".$id."')";
            if ($this->connection->query($sql) === FALSE) {
		echo "Error: " . $sql . "<br>" . $this->connection->error;
                $errorCount++;
            }else{
                $sumRecords++;
            }
	}
        $this->closeConnection();
        $countArray = array($errorCount, $sumRecords);
        return $countArray;
        
    }
    
    public function grabFacebookPagesIDs(&$rowCount){
        $this->openConnection();
        $idArray = array();
        $sql="SELECT page_id FROM page_names";
        $result=$this->connection->query($sql);
        if ($result === FALSE) {
		echo "Error: " . $sql . "<br>" . $this->connection->error;
                die;
            }else{
                while($row = $result->fetch_assoc()){
                    $rowCount++;
                    array_push($idArray, $row["page_id"]);
                }
                return $idArray;
                /*testing part bellow:
                echo "Number of rows pushed in to the db: ".$rowCounter;
                for ($i = 0; $i<10; $i++){
                    echo "<br>".$idArray[$i];
                }*/
            }
    }
    
    
    
    public function openConnection(){
        $this->connection = new mysqli($this->servername,$this->username,$this->password,$this->dbname);
        if($this->connection->connect_errno){
            echo "Connection failed badly. I mean really Bad!";
            die;
        }
    }
    
    public function closeConnection(){
        $this->connection->close();
    }
}